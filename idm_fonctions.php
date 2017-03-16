<?php
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


function idm_create_repository_img() {
	include_spip('base/abstract_sql');
	$extensions = sql_allfetsel('DISTINCT extension', 'spip_documents');
	foreach ($extensions as $extension) {
		if (!is_dir(_DIR_IMG . $extension['extension'])) {
			mkdir(_DIR_IMG . $extension['extension']);
			chmod(_DIR_IMG . $extension['extension'], _SPIP_CHMOD);
		}
	}

}

/**
 * Cette fonction va créer un fichier `prod_importer_documents.sh` à la racine du site.
 * Ce fichier contiendra un script bash pour importer tous les documents du site (cf. stockés dans la table `spip_documents`) et en allant les chercher sur le site source par `http`.
 * Il ne faut pas oublier de renseigner l'url du site source dans le formulaire `?exec=configurer_idm`
 * De plus, si le site source est la même url que le présent site, la fonction ne sera pas exécutée pour des questions de pertinence et de paradoxe.
 * Dans le script généré, les actions seront les suivantes :
 * - Vérifier que le répertoire de l'extension du document est bien présent, sinon on le crée. Action unique par extension ;
 * - Cela fait, se déplacer dans le répertoire de l'extension ;
 * - Vérifier que le document existe ou pas ;
 * -- Si le document existe, pas d'importation et afficher un message indiquant son existence ;
 * -- Si le document n'existe pas, on essaie son importation :
 * --- Si le document est accessible sur le site http://source.tld/, copier le fichier ;
 * --- Si le document n'est pas accessible, afficher un message.
 *
 * @return bool
 */
function idm_command_line() {
	include_spip('base/abstract_sql');
	include_spip('inc/config');
	idm_create_repository_img();
	$adresse_site = lire_config('adresse_site');
	$config_idm = lire_config('idm/source');
	$dir_img_server = $_SERVER['DOCUMENT_ROOT'] . '/' . preg_replace("/\.\.\//", '', _DIR_IMG);
	$dir_img = preg_replace("/\.\.\//", '/', _DIR_IMG);
	/**
	 * Si l'url source est la même que le présent site,
	 * la fonction ne se lancera pas.
	 */
	if (preg_match('/^http/', $config_idm) and !preg_match("/^" . $config_idm . "/", $adresse_site)) {
		$documents = sql_allfetsel('fichier, extension', 'spip_documents', "distant='non'", '', 'extension');
		/**
		 * Si on a des documents, on peut procéder à l'alimentation du script sh
		 */
		if (is_array($documents) and count($documents) > 0) {
			$command_line = array();
			$command_line[] = "#!/bin/bash";
			$command_line = array_merge($command_line, idm_formater_command_documents($documents, $dir_img_server, $dir_img));

			$command_line[] = 'scriptpath="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"';
			$command_line[] = 'rm -rf ${scriptpath}/"${BASH_SOURCE[0]}"'; // auto-delete du script à la fin de son exécution
			$command_line = array_unique($command_line); // ne pas avoir d'action en double (cf. répertoire d'extension)
			spip_log(count($command_line) . " lignes de commande", 'idm');
			$command_line = implode("\n", $command_line);

			try {
				$handle = fopen(_DIR_TMP . "import_medias.sh", 'w');
				fwrite($handle, $command_line);
				fclose($handle);
				spip_log('Le fichier import_medias.sh a été créé ici : ' . _DIR_TMP . 'import_medias.sh', 'idm');

				return true;
			} catch (Exception $e) {
				echo 'Caught exception: ', $e->getMessage(), "\n";
			}
		}
	}

	return false;
}

function idm_bash_objet($_objets = 'articles') {
	include_spip('inc/config');
	include_spip('inc/chercher_logo');
	$spip_version = spip_version();
	$spip_num = intval($spip_version);
	if ($spip_num == 2) {
		include_spip('base/connect_sql');
	} else {
		include_spip('base/abstract_sql');
		include_spip('base/objets');
	}
	$adresse_site = lire_config('adresse_site');
	$config_idm = lire_config('idm/source');
	/**
	 * Si l'url source est la même que le présent site,
	 * la fonction ne se lancera pas.
	 */
	if (preg_match('/^http/', $config_idm) and !preg_match("/^" . $config_idm . "/", $adresse_site)) {
		$dir_img_server = $_SERVER['DOCUMENT_ROOT'] . preg_replace("/\.\.\//", '/', _DIR_IMG);
		$dir_img = preg_replace("/\.\.\//", '/', _DIR_IMG);
		$objet = objet_type($_objets);
		$table_objet_sql = table_objet_sql($objet);
		$id_table_objet = id_table_objet($objet);
		$type_logo = type_du_logo($id_table_objet);
		$modes_logos = array('on', 'off');
		global $formats_logos;

		$objets_bdd = sql_allfetsel($id_table_objet, $table_objet_sql, '', '', $id_table_objet);

		/**
		 * On a bien des objets enregistrés en BDD,
		 * donc on peut travailler
		 */
		if (is_array($objets_bdd) and count($objets_bdd) > 0) {
			$command_line = array();
			$command_line[] = "#!/bin/bash";
			$command_line[] = "cd " . $dir_img_server;

			foreach ($objets_bdd as $objet_bdd) {
				foreach ($modes_logos as $mode) {
					foreach ($formats_logos as $format) {
						$logo_objet = $type_logo . $mode . $objet_bdd[$id_table_objet] . '.' . $format;
						$command_line[] = 'if [ -f ' . $dir_img_server . $logo_objet . ' ]; then echo "Le fichier ' . $dir_img_server . $logo_objet . ' existe" ; else wget --spider -v ' . $config_idm . $dir_img . $logo_objet . ' && wget ' . $config_idm . $dir_img . $logo_objet . ' || echo "Le fichier ' . $config_idm . $dir_img . $logo_objet . ' n\'est pas accessible" ; fi';
					}
				}
			}
			$command_line[] = 'scriptpath="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"';
			$command_line[] = 'rm -rf ${scriptpath}/"${BASH_SOURCE[0]}"'; // auto-delete du script à la fin de son exécution
			$command_line = array_unique($command_line); // ne pas avoir d'action en double
			spip_log(count($command_line) . " lignes de commande", 'idm');
			$command_line = implode("\n", $command_line);

			try {
				$handle = fopen(_DIR_TMP . 'import_logos_'. $objet .'.sh', 'w');
				fwrite($handle, $command_line);
				fclose($handle);
				spip_log('Le fichier  ' . 'import_logos_'. $objet . '.sh a été créé ici : ' . _DIR_TMP  . 'import_logos_'. $objet .'.sh', 'idm');

				return true;
			} catch (Exception $e) {
				echo 'Caught exception: ', $e->getMessage(), "\n";
			}
		}
	}

}

function idm_nom_tables_principales() {
	$tables_principales = $GLOBALS['tables_principales'];
	$tables_principales = array_keys($tables_principales);

	return $tables_principales;
}

function idm_formater_command_documents($documents, $dir_img_server, $dir_img) {
	if (!is_array($documents)) {
		return false;
	}
	$command_line = array();
	include_spip('base/abstract_sql');
	include_spip('inc/config');
	$config_idm = lire_config('idm/source');

	foreach ($documents as $document) {
		$command_line[] = "if [ -d " . $dir_img_server . $document['extension'] . '/ ]; then echo ""; else mkdir -p ' . $dir_img_server . $document['extension'] . '/ ; fi';
		$command_line[] = "cd " . $dir_img_server . $document['extension'] . '/';
		$command_line[] = 'if [ -f ' . $dir_img_server . $document['fichier'] . ' ]; then echo "Le fichier ' . $dir_img_server . $document['fichier'] . ' existe" ; else wget --spider -v ' . $config_idm . $dir_img . $document['fichier'] . ' && wget ' . $config_idm . $dir_img . $document['fichier'] . ' || echo "Le fichier ' . $config_idm . $dir_img . $document['fichier'] . ' n\'est pas accessible" ; fi';
	}

	return $command_line;
}
