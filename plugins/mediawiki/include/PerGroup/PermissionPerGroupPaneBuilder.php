<?php
/**
 * Copyright (c) Enalean, 2018. All Rights Reserved.
 *
 * This file is a part of Tuleap.
 *
 * Tuleap is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * Tuleap is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Tuleap. If not, see <http://www.gnu.org/licenses/>.
 */

namespace Tuleap\Mediawiki\PerGroup;

use MediawikiManager;
use MediawikiUserGroupsMapper;
use Project;
use ProjectUGroup;
use Tuleap\Project\Admin\PerGroup\PermissionPerGroupPanePresenter;
use Tuleap\Project\Admin\PerGroup\PermissionPerGroupUGroupFormatter;
use Tuleap\Project\Admin\Permission\PermissionPerGroupCollection;
use Tuleap\Project\Admin\Permission\PermissionPerGroupPaneCollector;
use UGroupManager;

class PermissionPerGroupPaneBuilder
{
    /**
     * @var MediawikiManager
     */
    private $mediawiki_manager;
    /**
     * @var UGroupManager
     */
    private $ugroup_manager;
    /**
     * @var PermissionPerGroupUGroupFormatter
     */
    private $formatter;

    /**
     * @var MediawikiUserGroupsMapper
     */
    private $mapper;

    public function __construct(
        MediawikiManager $mediawiki_manager,
        UGroupManager $ugroup_manager,
        PermissionPerGroupUGroupFormatter $formatter,
        MediawikiUserGroupsMapper $mediawiki_user_groups_mapper
    ) {
        $this->mediawiki_manager = $mediawiki_manager;
        $this->ugroup_manager    = $ugroup_manager;
        $this->formatter         = $formatter;
        $this->mapper            = $mediawiki_user_groups_mapper;
    }

    public function buildPresenter(PermissionPerGroupPaneCollector $event)
    {
        $project = $event->getProject();

        $selected_group = $event->getSelectedUGroupId();
        $ugroup         = $this->ugroup_manager->getUGroup($event->getProject(), $selected_group);

        $permissions = new PermissionPerGroupCollection();

        $this->addReadersToCollection($project, $permissions, $ugroup);
        $this->addWritersToCollection($project, $permissions, $ugroup);
        $this->addMediawikiSpecificPermissionsToCollection($project, $permissions, $ugroup);

        return new PermissionPerGroupPanePresenter(
            $permissions->getPermissions(),
            $ugroup
        );
    }

    /**
     * @return array
     */
    private function addReadersToCollection(
        Project $project,
        PermissionPerGroupCollection $collection,
        ProjectUGroup $ugroup = null
    ) {
        if ($ugroup) {
            $readers = $this->mediawiki_manager->getReadAccessControlForProjectContainingGroup($project, $ugroup);
        } else {
            $readers = $this->mediawiki_manager->getReadAccessControl($project);
        }

        if ($readers) {
            $this->formatUGroupPermissions($project, $readers, dgettext('tuleap-mediawiki', 'Readers'), $collection);
        }
    }

    /**
     * @return array
     */
    private function addWritersToCollection(
        Project $project,
        PermissionPerGroupCollection $collection,
        ProjectUGroup $ugroup = null
    ) {
        if ($ugroup) {
            $writers = $this->mediawiki_manager->getWriteAccessControlForProjectContainingUGroup($project, $ugroup);
        } else {
            $writers = $this->mediawiki_manager->getWriteAccessControl($project);
        }

        if ($writers) {
            $this->formatUGroupPermissions($project, $writers, dgettext('tuleap-mediawiki', 'Writers'), $collection);
        }
    }

    private function addMediawikiSpecificPermissionsToCollection(
        Project $project,
        PermissionPerGroupCollection $collection,
        ProjectUGroup $selected_ugroup = null
    ) {
        $current_mapping = $this->mapper->getCurrentUserGroupMapping($project);

        foreach (MediawikiUserGroupsMapper::$MEDIAWIKI_MODIFIABLE_GROUP_NAMES as $mw_group_name) {
            $this->addPermissionRelativeToSearch(
                $project,
                $collection,
                $current_mapping,
                $mw_group_name,
                $selected_ugroup
            );
            $this->addAllPermissionsWhenNoSearchCriteriaIsDefined(
                $project,
                $collection,
                $current_mapping,
                $mw_group_name,
                $selected_ugroup
            );
        }
    }

    private function formatUGroupPermissions(
        Project $project,
        array $permissions,
        $group_name,
        PermissionPerGroupCollection $collection
    ) {
        $formatted_group = array();
        foreach ($permissions as $mapped_ugroup) {
            $formatted_group[] = $this->formatter->formatGroup($project, $mapped_ugroup);
        }

        $collection->addPermissions(
            array('name' => $group_name, 'groups' => $formatted_group, 'url' => $this->getGlobalAdminLink($project))
        );
    }

    /**
     * @param Project                      $project
     * @param PermissionPerGroupCollection $collection
     * @param ProjectUGroup                $selected_ugroup
     * @param                              $current_mapping
     * @param                              $mw_group_name
     */
    private function addPermissionRelativeToSearch(
        Project $project,
        PermissionPerGroupCollection $collection,
        $current_mapping,
        $mw_group_name,
        ProjectUGroup $selected_ugroup = null
    ) {
        if (! $selected_ugroup) {
            return;
        }

        if (in_array($selected_ugroup->getId(), $current_mapping[$mw_group_name])) {
            $this->formatUGroupPermissions(
                $project,
                $current_mapping[$mw_group_name],
                $GLOBALS['Language']->getText('plugin_mediawiki', 'group_name_'.$mw_group_name),
                $collection
            );
        }
    }

    /**
     * @param Project                      $project
     * @param PermissionPerGroupCollection $collection
     * @param ProjectUGroup                $selected_ugroup
     * @param                              $current_mapping
     * @param                              $mw_group_name
     */
    private function addAllPermissionsWhenNoSearchCriteriaIsDefined(
        Project $project,
        PermissionPerGroupCollection $collection,
        $current_mapping,
        $mw_group_name,
        ProjectUGroup $selected_ugroup = null
    ) {
        if ($selected_ugroup) {
            return;
        }

        $this->formatUGroupPermissions(
            $project,
            $current_mapping[$mw_group_name],
            $GLOBALS['Language']->getText('plugin_mediawiki', 'group_name_'.$mw_group_name),
            $collection
        );
    }

    private function getGlobalAdminLink(Project $project)
    {
        return MEDIAWIKI_BASE_URL . "/forge_admin.php?" . http_build_query(
            [
                "group_id" => $project->getID()
            ]
        );
    }
}
