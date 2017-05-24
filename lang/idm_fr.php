<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// A
	'action_label' => 'Action',

	// B
	'btn_generate_bash' => 'Générer',
	'btn_regenerate_bash' => 'Régénérer',
	'btn_supprimer_bash' => 'Supprimer',

	// C
	'cfg_titre_parametrages' => 'Paramétrages',
	'cfg_url_source_label' => 'URL source',
	'cfg_url_source_explication' => 'URL vers le répertoire "IMG/" du site source. <br/>Exemple&nbsp;: http://example.tld/IMG/',
	'cfg_url_source_explication_longue' => 'Veuillez renseigner ci-dessous l’url http vers le répertoire IMG/ du site source. Cela permettra de récupérer certaines informations telles que les documents ou les logos si nécessaire.',

	// E
	'environnement_source_info' => 'Vous êtes sur l’environnement source',

	// F
	'fichier_bash_idm_creation_ko' => 'Le fichier bash n’a pu être créé',
	'fichier_bash_idm_creation_ok' => 'Le fichier bash a été créé avec succès.',
	'fichier_bash_idm_existant' => 'Fichier existant.',
	'fichier_bash_idm_inexistant' => 'Fichier inexistant.',
	'fichier_bash_idm_creation' => 'Créé le',
	'fichier_bash_label' => 'Fichier bash',

	// I
	'idm_icone_label' => 'Importer les médias de la source',
	'idm_titre' => 'Script d’importation des documents',
	'idm_explication' => 'Depuis cette page, vous pourrez générer les fichiers bash désirés.<br/>Un administrateur devra exécuter ce script en lignes de commande pour importer les documents sur le serveur du présent site. Toutefois, si l’url du site source est la même que le présent site, le script ne sera pas créé pas mesure de sécurité.',

	// N
	'nom_objets_label' => 'Nom des objets',

	// T
	'table_vide' => 'Il n’y pas d’entrées dans la table.',
	'titre_idm' => 'Importation de Médias',
	'titre_page_configurer_idm' => 'Page de configuration',

	// U
	'url_source_verif' => 'L’url source n’est pas conforme à ce qui est attendu. Elle doit se terminer par "IMG/".',
	'url_source_identique' => 'L’url source ne peut être la même que le présent site.',
);
