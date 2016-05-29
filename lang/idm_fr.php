<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// C
	'cfg_titre_parametrages' => 'Paramétrages',
	'cfg_url_source_label' => 'URL source',
	'cfg_url_source_explication' => 'Veuillez renseigner ci-dessous l\'url du site source. Cela permettra de récupérer certaines informations telles que les documents ou les logos si nécessaire. <strong>Ne pas mettre de "/" à la fin de l\'url.</strong>',

	// E
	'environnement_source_info' => 'Vous êtes sur l\'environnement source',

	// F
	'fichier_bash_idm_creation_ko' => 'Le fichier bash n\'a pu être créé',
	'fichier_bash_idm_creation_ok' => 'Le fichier bash a été créé avec succès.',

	// I
	'idm_icone_label' => 'Importer les médias de la source',
	'idm_titre' => 'Script d\'importation des documents',
	'idm_explication' => 'A chaque chargement de cette page, un fichier script va être créé à la racine du site. Si un fichier existe déjà, il sera écrasé et remplacé.<br/>Un administrateur devra exécuter ce script en lignes de commande pour importer les documents sur le serveur du présent site. Toutefois, si l\'url du site source est la même que le présent site, le script ne sera pas créé pas mesure de sécurité.',

	// T
	'titre_idm' => 'Importation de Médias',
	'titre_page_configurer_idm' => 'Page de configuration',
);
