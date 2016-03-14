<?php
/**
 * Created by PhpStorm.
 * User: teddypayet
 * Date: 14/03/2016
 * Time: 17:35
 */

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
	$dir_img_server = $_SERVER['DOCUMENT_ROOT'] . preg_replace("/\.\.\//", '/', _DIR_IMG);
	$dir_img = preg_replace("/\.\.\//", '/', _DIR_IMG);
	/**
	 * Si l'url source est la même que le présent site,
	 * la fonction ne se lancera pas.
	 */
	if (preg_match('/^http/', $config_idm) and !preg_match("/^" . $config_idm . "/", $adresse_site)) {
		$documents = sql_allfetsel('fichier, extension', 'spip_documents', '', '', 'extension');

		/**
		 * Si on a des documents, on peut procéder à l'alimentation du script sh
		 */
		if (is_array($documents) and count($documents) > 0) {
			$command_line = array();
			$command_line[] = "#!/bin/bash";
			foreach ($documents as $document) {
				$command_line[] = "if [ -d " . $dir_img_server . $document['extension'] . '/ ]; then echo ""; else mkdir -p ' . $dir_img_server . $document['extension'] . '/ ; fi';
				$command_line[] = "cd " . $dir_img_server . $document['extension'] . '/';
				$command_line[] = 'if [ -f ' . $dir_img_server . $document['fichier'] . ' ]; then echo "Le fichier ' . $dir_img_server . $document['fichier'] . ' existe" ; else wget --spider -v ' . $config_idm . $dir_img . $document['fichier'] . ' && wget ' . $config_idm . $dir_img . $document['fichier'] . ' || echo "Le fichier ' . $config_idm . $dir_img . $document['fichier'] . ' n\'est pas accessible" ; fi';
			}
			$command_line = array_unique($command_line); // ne pas avoir d'action en double (cf. répertoire d'extension)
			$command_line = implode("\n", $command_line);

			try {
				$handle = fopen(_DIR_RACINE . "import_medias.sh", 'w');
				fwrite($handle, $command_line);
				fclose($handle);

				return true;
			} catch (Exception $e) {
				echo 'Caught exception: ', $e->getMessage(), "\n";
			}
		}
	}

	return false;
}
