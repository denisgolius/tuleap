<?php
// @codingStandardsIgnoreFile
// @codeCoverageIgnoreStart
// this is an autogenerated file - do not edit
function autoload9752249b28f54466f1c5a8222f8b05d6($class) {
    static $classes = null;
    if ($classes === null) {
        $classes = array(
            'mediawiki_migration_mediawikimigrator' => '/Migration/MediawikiMigrator.php',
            'mediawiki_unsupportedlanguageexception' => '/UnsupportedLanguageException.php',
            'mediawiki_unsupportedversionexception' => '/UnsupportedVersionException.php',
            'mediawikiadmincontroller' => '/MediawikiAdminController.class.php',
            'mediawikiadminlanguagepanepresenter' => '/MediawikiAdminLanguagePanePresenter.php',
            'mediawikiadminpanepresenter' => '/MediawikiAdminPanePresenter.php',
            'mediawikiadminpermissionspanepresenter' => '/MediawikiAdminPermissionsPanePresenter.class.php',
            'mediawikidao' => '/MediawikiDao.class.php',
            'mediawikifusionforgeprojectnameretriever' => '/MediawikiFusionForgeProjectNameRetriever.php',
            'mediawikigrouppresenter' => '/MediawikiGroupPresenter.class.php',
            'mediawikigroups' => '/MediawikiGroups.class.php',
            'mediawikiinstantiater' => '/MediawikiInstantiater.class.php',
            'mediawikiinstantiaterexception' => '/MediawikiInstantiaterException.class.php',
            'mediawikilanguagedao' => '/MediawikiLanguageDao.php',
            'mediawikilanguagemanager' => '/MediawikiLanguageManager.php',
            'mediawikimanager' => '/MediawikiManager.class.php',
            'mediawikimlebextensiondao' => '/MediawikiMLEBExtensionDao.php',
            'mediawikimlebextensionmanager' => '/MediawikiMLEBExtensionManager.php',
            'mediawikimlebextensionmanagerloader' => '/MediawikiMLEBExtensionManagerLoader.php',
            'mediawikiplugin' => '/mediawikiPlugin.class.php',
            'mediawikiplugindescriptor' => '/MediaWikiPluginDescriptor.class.php',
            'mediawikiplugininfo' => '/MediaWikiPluginInfo.class.php',
            'mediawikisiteadminallowedprojectspresenter' => '/MediawikiSiteAdminAllowedProjectsPresenter.class.php',
            'mediawikisiteadmincontroller' => '/MediawikiSiteAdminController.class.php',
            'mediawikisiteadminresourcerestrictor' => '/MediawikiSiteAdminResourceRestrictor.php',
            'mediawikisiteadminresourcerestrictordao' => '/MediawikiSiteAdminResourceRestrictorDao.php',
            'mediawikiusergroupsmapper' => '/MediawikiUserGroupsMapper.class.php',
            'mediawikiversiondao' => '/MediawikiVersionDao.php',
            'mediawikiversionmanager' => '/MediawikiVersionManager.php',
            'mediawikixmlimporter' => '/MediaWikiXMLImporter.class.php',
            'pluginspecificrolesetting' => '/PluginSpecificRoleSettings.php',
            'servicemediawiki' => '/ServiceMediawiki.class.php',
            'systemevent_mediawiki_switch_to_123' => '/events/SytemEvent_MEDIAWIKI_SWITCH_TO_123.class.php',
            'tuleap\\mediawiki\\events\\systemevent_mediawiki_to_central_db' => '/events/SystemEvent_MEDIAWIKI_TO_CENTRAL_DB.php',
            'tuleap\\mediawiki\\forgeusergrouppermission\\mediawikiadminallprojects' => '/ForgeUserGroupPermission/MediawikiAdminAllProjects.class.php',
            'tuleap\\mediawiki\\maintenance\\cleanunused' => '/Maintenance/CleanUnused.php',
            'tuleap\\mediawiki\\maintenance\\cleanunuseddao' => '/Maintenance/CleanUnusedDao.php',
            'tuleap\\mediawiki\\mediawikidatadir' => '/MediawikiDataDir.php',
            'tuleap\\mediawiki\\migration\\movetocentraldbdao' => '/Migration/MoveToCentralDbDao.php',
            'tuleap\\mediawiki\\pergroup\\permissionpergroupcollection' => '/PerGroup/PermissionPerGroupCollection.php',
            'tuleap\\mediawiki\\pergroup\\permissionpergrouppanebuilder' => '/PerGroup/PermissionPerGroupPaneBuilder.php'
        );
    }
    $cn = strtolower($class);
    if (isset($classes[$cn])) {
        require dirname(__FILE__) . $classes[$cn];
    }
}
spl_autoload_register('autoload9752249b28f54466f1c5a8222f8b05d6');
// @codeCoverageIgnoreEnd
