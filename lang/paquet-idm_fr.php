<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// Fichier source, a modifier dans svn://zone.spip.org/spip-zone/_core_/plugins/urls_etendues/lang/
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// A
	'idm_description' => 'Ce plugin permet l\'importation de documents d\'un site par consultation HTTP. En effet, il n\'est pas toujours possible d\'avoir un accès SSH ou FTP d\'un site. Le seul accès étant l\'accès du site par consultation de l\'url public. Pour se faire, le plugin regarde tous les documents, non-distants, stockés dans la table <em>spip_documents"</em> et construit l\'url selon le schéma <em>http://source.tld/IMG/ext/fichier.ext</em>. Si le fichier existe, alors on le copie dans le répertoire local <em>IMG/ext/fichier.ext</em>. Si un fichier est déjà présent localement, il ne sera pas copié et passe au document suivant.',
	'idm_nom' => 'Importer des médias',
	'idm_slogan' => 'Simplifier l\'importation des médias',

);
