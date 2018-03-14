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
 * Cette fonction va créer un fichier `importer_medias.sh` dans le répertoire `tmp/` du site.
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
function idm_bash_medias() {
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
	if (preg_match(',^http,', $config_idm) and !preg_match(",^" . $config_idm . ",", $adresse_site)) {
		$documents = sql_allfetsel('fichier, extension', 'spip_documents', "distant='non'", '', 'extension');
		/**
		 * Si on a des documents, on peut procéder à l'alimentation du script sh
		 */
		if (is_array($documents) and count($documents) > 0) {
			idm_bash_file_delete('spip_documents');
			$command_line = array();
			$command_line[] = "#!/bin/bash";
			$command_line = array_merge($command_line, idm_formater_command_documents($documents, $dir_img_server, $dir_img));

			$command_line[] = 'scriptpath="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"';
			$command_line[] = 'rm -rf ${scriptpath}/"${BASH_SOURCE[0]}"'; // auto-delete du script à la fin de son exécution
			$command_line = array_unique($command_line); // ne pas avoir d'action en double (cf. répertoire d'extension)
			spip_log(count($command_line) . " lignes de commande", 'idm');
			$command_line = implode("\n", $command_line);

			// On écrit le fichier rempli de ses lignes de commandes
			return idm_bash_file_create(idm_bash_file_prepare('spip_documents'), $command_line);
		}
	}

	return false;
}

function idm_bash_objet($_objets = 'articles') {
	include_spip('inc/config');
	include_spip('inc/chercher_logo');
	include_spip('inc/filtres');
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
	if (preg_match(',^http,', $config_idm) and !preg_match(",^" . $config_idm . ",", $adresse_site)) {
		$dir_img_server = $_SERVER['DOCUMENT_ROOT'] . preg_replace("/\.\.\//", '/', _DIR_IMG);
		$dir_img = preg_replace("/\.\.\//", '/', _DIR_IMG);
		$objet = objet_type($_objets); /* Une sécurité pour récupérer le bon objet */
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
			$count_objets = count($objets_bdd);

			foreach ($objets_bdd as $index => $objet_bdd) {
				foreach ($modes_logos as $mode) {
					foreach ($formats_logos as $format) {
						$logo_objet = $type_logo . $mode . $objet_bdd[$id_table_objet] . '.' . $format;
						$command_line[] = 'echo ""; echo "' . ($index + 1) . '/' . $count_objets . '"; if [ -f ' . $dir_img_server . $logo_objet . ' ]; then echo "Le fichier ' . $dir_img_server . $logo_objet . ' existe" ; else wget --spider -v ' . $config_idm . $logo_objet . ' && wget ' . $config_idm . $logo_objet . ' || echo "Le fichier ' . $config_idm . $logo_objet . ' n\'est pas accessible" ; fi';
					}
				}
			}
			$command_line[] = 'scriptpath="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"';
			$command_line[] = 'rm -rf ${scriptpath}/"${BASH_SOURCE[0]}"'; // auto-delete du script à la fin de son exécution
			$command_line = array_unique($command_line); // ne pas avoir d'action en double
			spip_log(count($command_line) . " lignes de commande", 'idm');
			$command_line = implode("\n", $command_line);

			// On écrit le fichier rempli de ses lignes de commandes
			return idm_bash_file_create(idm_bash_file_prepare($_objets), $command_line);
		}
	}

	return false;
}

function idm_bash_spip() {
	include_spip('inc/config');
	include_spip('inc/chercher_logo');
	include_spip('inc/filtres');
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
	$dir_img_server = $_SERVER['DOCUMENT_ROOT'] . preg_replace("/\.\.\//", '/', _DIR_IMG);
	$modes_logos = array('on', 'off');
	global $formats_logos;
	$objets_bdd = array('site', 'rub');
	$command_line = array();
	$command_line[] = "#!/bin/bash";
	$command_line[] = "cd " . $dir_img_server;
	$count_objets = count($objets_bdd);
	foreach ($objets_bdd as $index => $objet_bdd) {
		foreach ($modes_logos as $mode) {
			foreach ($formats_logos as $format) {
				$logo_objet = $objet_bdd . $mode . '0.' . $format;
				$command_line[] = 'echo ""; echo "' . ($index + 1) . '/' . $count_objets . '"; if [ -f ' . $dir_img_server . $logo_objet . ' ]; then echo "Le fichier ' . $dir_img_server . $logo_objet . ' existe" ; else wget --spider -v ' . $config_idm . $logo_objet . ' && wget ' . $config_idm . $logo_objet . ' || echo "Le fichier ' . $config_idm . $logo_objet . ' n\'est pas accessible" ; fi';
			}
		}
	}
	$command_line[] = 'scriptpath="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"';
	$command_line[] = 'rm -rf ${scriptpath}/"${BASH_SOURCE[0]}"'; // auto-delete du script à la fin de son exécution
	$command_line = array_unique($command_line); // ne pas avoir d'action en double
	spip_log(count($command_line) . " lignes de commande", 'idm');
	$command_line = implode("\n", $command_line);

	return idm_bash_file_create(idm_bash_file_prepare('spip'), $command_line);
}

/**
 * Lister les tables principales de SPIP
 *
 * @return array tableau contenant le nom de chaque table principale
 */
function idm_nom_tables_principales() {
	if (function_exists('lister_tables_objets_sql')) {
		/* On est en SPIP >= 3.0 */
		include_spip('base/objets');
		$tables_principales = lister_tables_objets_sql();
	} else {
		$tables_principales = $GLOBALS['tables_principales'];
	}
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
	$count_documents = count($documents);

	foreach ($documents as $index => $document) {
		$command_line[] = "if [ -d " . $dir_img_server . $document['extension'] . '/ ]; then echo ""; else mkdir -p ' . $dir_img_server . $document['extension'] . '/ ; fi';
		$command_line[] = "cd " . $dir_img_server . $document['extension'] . '/';
		$command_line[] = 'echo ""; echo "' . ($index + 1) . '/' . $count_documents . '"; if [ -f ' . $dir_img_server . $document['fichier'] . ' ]; then echo "Le fichier ' . $dir_img_server . $document['fichier'] . ' existe" ; else wget --spider -v ' . $config_idm . $document['fichier'] . ' && wget ' . $config_idm . $document['fichier'] . ' || echo "Le fichier ' . $config_idm . $document['fichier'] . ' n\'est pas accessible" ; fi';
	}

	return $command_line;
}

/**
 * Vérifier si le fichier bash de l'objet est présent.
 *
 * @param string $_objets table sql de l'objet désiré. Exemple : spip_documents
 * @return bool true si le fichier existe.
 */
function idm_bash_file_presence($_objets = 'spip_documents') {
	$fichier = idm_bash_file_prepare($_objets);
	if ($fichier === false) {
		return false;
	}

	if (is_file($fichier)) {
		return true;
	}

	return false;
}

/**
 * Supprimer le fichier bash de l'objet s'il existe.
 *
 * @param string $_objets nom de l'objet sous la forme : `spip_objets
 * @return bool true si la suppression du fichier bash a pu se faire
 *  false
 * - si aucun objet n'est renseigné en paramètre de la fonction
 * - si le fichier bash n'existe pas.
 * - si le fichier existe mais n'a pu être supprimé.
 */
function idm_bash_file_delete($_objets = 'spip_documents') {
	$fichier = idm_bash_file_prepare($_objets);
	if ($fichier === false) {
		return false;
	}

	if (idm_bash_file_presence($_objets)) {
		include_spip('inc/flock');

		return supprimer_fichier($fichier);
	}

	return false;
}

/**
 * Récupérer la date de création du fichier bash de l'objet
 *
 * @param string $_objets
 * @return bool|string
 */
function idm_bash_file_date($_objets = 'spip_documents') {

	$fichier = idm_bash_file_prepare($_objets);
	if ($fichier === false) {
		return false;
	}
	if (idm_bash_file_presence($_objets)) {

		return date("Y-m-d H:i:s", filectime($fichier));
	}

	return false;
}

/**
 * Construire le nom du fichier bash de l'objet.
 *
 * @param string $_objets
 * @return bool|string
 */
function idm_bash_file_prepare($_objets = 'spip_documents') {
	if (empty($_objets) or is_null($_objets) or !is_string($_objets)) {
		trigger_error("Le paramètre de la fonction " . __FUNCTION__ . " n'est pas une chaine de caractères.", E_USER_ERROR);

		return false;
	}
	include_spip('inc/config');
	include_spip('inc/filtres');
	$spip_version = spip_version();
	$spip_num = intval($spip_version);
	if ($spip_num == 2) {
		include_spip('base/connect_sql');
	} else {
		include_spip('base/abstract_sql');
		include_spip('base/objets');
	}
	if ($_objets === 'spip_documents') {
		$fichier = _DIR_TMP . 'import_medias.sh';
	} else if ($_objets === 'spip') {
		$fichier = _DIR_TMP . 'import_medias_spip.sh';
	} else {
		$objet = objet_type($_objets); /* Une sécurité pour récupérer le bon objet */
		$fichier = _DIR_TMP . 'import_logos_' . $objet . '.sh';
	}

	return $fichier;
}

/**
 * Vérifier s'il y a des enregistrements faits dans la table de l'objet.
 *
 * @param string $_objets
 * @return bool
 */
function idm_test_objet_vide($_objets = 'spip_documents') {
	if (empty($_objets) or is_null($_objets) or !is_string($_objets)) {
		trigger_error("Le paramètre de la fonction " . __FUNCTION__ . " n'est pas une chaine de caractères.", E_USER_ERROR);

		return false;
	}
	include_spip('inc/filtres');
	$spip_version = spip_version();
	$spip_num = intval($spip_version);
	if ($spip_num == 2) {
		include_spip('base/connect_sql');
	} else {
		include_spip('base/abstract_sql');
		include_spip('base/objets');
	}
	$objet = objet_type($_objets); /* Une sécurité pour récupérer le bon objet */
	$table_objet_sql = table_objet_sql($objet);

	$compteur = sql_countsel($table_objet_sql);

	if (empty($compteur) or $compteur === false or $compteur === 0) {
		return true;
	}

	return false;
}

/**
 * Créer le fichier bash avec ses lignes de commandes
 *
 * @param string $fichier Chemin vers le fichier bash
 * @param string $commands_line le contenu à mettre dans le fichier bash
 * @return bool
 */
function idm_bash_file_create($fichier = _DIR_TMP . 'test.sh', $commands_line = '') {

	if (is_array($commands_line)) {
		$commands_line = implode("\n", $commands_line);
	}
	try {
		$handle = fopen($fichier, 'w');
		fwrite($handle, $commands_line);
		fclose($handle);
		spip_log('Le fichier  ' . basename($fichier) . ' a été créé.' . $fichier, 'idm');

		return true;
	} catch (Exception $e) {
		echo 'Caught exception: ', $e->getMessage(), "\n";
	}

}