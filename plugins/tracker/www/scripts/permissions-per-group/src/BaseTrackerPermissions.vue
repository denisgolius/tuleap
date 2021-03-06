/**
* Copyright Enalean (c) 2018. All rights reserved.
*
* Tuleap and Enalean names and logos are registrated trademarks owned by
* Enalean SAS. All other trademarks or names are properties of their respective
* owners.
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

(<template>
    <section class="tlp-pane-section">
        <div class="tlp-alert-danger" v-if="hasError">
            {{ error }}
        </div>

        <button class="tlp-button-primary tlp-button-outline"  v-if="isButtonLoadAllDisplayed"
                v-on:click="loadAll"
        >
            {{ button_tracker_permissions_label }}
        </button>

        <div class="permission-per-group-loader" v-if="is_loading"></div>

        <tracker-permissions-table v-if="is_loaded"
            v-bind:tracker-permissions="tracker_permissions"
            v-bind:selected-ugroup-name="selectedUgroupName"
        >
        </tracker-permissions-table>
    </section>
</template>)
(<script>
    import { getTrackerPermissions } from './rest-querier.js';
    import { gettext_provider }      from './gettext-provider.js';
    import TrackerPermissionsTable   from './TrackerPermissionsTable.vue';

    export default {
        name : 'TrackerPermissions',
        components: {
            TrackerPermissionsTable
        },
        props: {
            selectedUgroupId  : String,
            selectedProjectId : String,
            selectedUgroupName: String
        },
        data() {
            return {
                is_loaded               : false,
                is_loading              : false,
                error                   : null,
                tracker_permissions: []
            };
        },
        computed: {
            hasError() {
                return this.error !== null;
            },
            isButtonLoadAllDisplayed() {
                return ! this.is_loaded && ! this.is_loading
            },
            button_tracker_permissions_label: () => gettext_provider.gettext('See all tracker permissions')
        },
        methods: {
            async loadAll() {
                try {
                    this.is_loading = true;

                    this.tracker_permissions = await getTrackerPermissions(
                        this.selectedProjectId,
                        this.selectedUgroupId
                    );

                    this.is_loaded = true;
                } catch (e) {
                    const { error } = await e.response.json();
                    this.error      = error;
                } finally {
                    this.is_loading = false;
                }
            }
        }
    };
</script>)
