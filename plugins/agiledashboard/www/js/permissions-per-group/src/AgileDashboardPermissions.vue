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

(
<template>
    <section class="tlp-pane-section">
        <div class="tlp-alert-danger" v-if="hasError">
            {{ error }}
        </div>

        <button class="tlp-button-primary tlp-button-outline"  v-if="displayButtonLoadAll"
                v-on:click="loadAll"
        > {{ planning_permissions }}
        </button>

        <div class="permission-per-group-loader" v-if="is_loading"></div>

        <table class="tlp-table"  v-if="is_loaded">
            <thead>
            <tr>
                <th> {{ planning }} </th>
                <th> {{ prioritizers }} </th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="permission in permissions" v-bind:key="permission.name">
                <td><a v-bind:href="permission.quick_link">{{ permission.name }}</a></td>
                <td>
                    <agile-dashboard-permissions-badge v-for="group in permission.ugroups"
                            v-bind:key="group.ugroup_name"
                            v-bind:is-project-admin="group.is_project_admin"
                            v-bind:is-static="group.is_static"
                            v-bind:is-custom="group.is_custom"
                            v-bind:group-name="group.ugroup_name"
                    >
                    </agile-dashboard-permissions-badge>
                </td>
            </tr>
            </tbody>
            <tbody v-if="isEmpty">
            <tr>
                <td colspan="2" v-if="hasASelectedUGroup" class="tlp-table-cell-empty">
                    {{ empty_state }}
                </td>
                <td colspan="2" v-else class="tlp-table-cell-empty">
                    {{ ugroup_empty_state }}
                </td>
            </tr>
            </tbody>
        </table>
    </section>
</template>)
(
<script>
    import AgileDashboardPermissionsBadge   from 'permission-badge/PermissionsPerGroupBadge.vue';
    import { gettext_provider }             from './gettext-provider.js';
    import { getAgiledashboardPermissions } from './rest-querier.js';
    import { sprintf }                      from 'sprintf-js';

    export default {
        components: {AgileDashboardPermissionsBadge},
        name: 'AgileDashboardPermissions',
        data() {
            return {
                is_loaded  : false,
                is_loading : false,
                permissions: [],
                error      : null,
            };
        },
        props: {
            selectedUgroupId  : String,
            selectedProjectId : String,
            selectedUgroupName: String
        },
        methods: {
            async loadAll() {
                try {
                    this.is_loading                 = true;
                    const { plannings_permissions } = await getAgiledashboardPermissions(this.selectedProjectId, this.selectedUgroupId);
                    this.is_loaded                  = true;
                    this.permissions                = plannings_permissions;
                } catch (e) {
                    const {error} = await e.response.json();
                    this.error    = error;
                } finally {
                    this.is_loading = false;
                }
            }
        },
        computed: {
            planning_permissions: () => gettext_provider.gettext("View planning permissions"),
            planning: ()             => gettext_provider.gettext("Planning"),
            prioritizers: ()         => gettext_provider.gettext("Who can prioritize?"),
            empty_state: ()          => gettext_provider.gettext("Agiledashboard has no planning defined"),
            ugroup_empty_state()   {
                return sprintf(
                    gettext_provider.gettext("%s has no permission for agiledashboard plannings"),
                    this.selectedUgroupName
                );
            },
            isEmpty() { return this.permissions.length === 0; },
            hasError() { return this.error !== null; },
            displayButtonLoadAll() { return ! this.is_loaded && ! this.is_loading },
            hasASelectedUGroup() { return this.selectedUgroupName === '' }
        }
    };
</script>)
