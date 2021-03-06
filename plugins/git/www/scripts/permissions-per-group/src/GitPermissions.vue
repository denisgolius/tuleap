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

        <button class="tlp-button-primary tlp-button-outline" v-if="displayButtonLoadAll"
                v-on:click="loadAll"
        > {{ repositories_permissions }}
        </button>

        <div class="permission-per-group-loader" v-if="is_loading"></div>

        <h2 class="tlp-pane-subtitle" v-if="is_loaded"> {{ repository_permissions_title }} </h2>
        <git-permissions-table
                v-bind:repositoryPermissions="permissions"
                v-bind:selectedUgroupName="selectedUgroupName"
                v-if="is_loaded"
        />
    </section>

</template>)
(
<script>
    import GitPermissionsTable   from './GitPermissionsTable.vue';
    import { gettext_provider }  from './gettext-provider.js';
    import { getGitPermissions } from './rest-querier.js';
    export default {
        components: {
            GitPermissionsTable,
        },
        name: 'GitPermissions',
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
                    this.is_loading        = true;
                    const { repositories } = await getGitPermissions(this.selectedProjectId, this.selectedUgroupId);
                    this.is_loaded         = true;
                    this.permissions       = repositories;
                } catch (e) {
                    const {error} = await e.response.json();
                    this.error    = error;
                } finally {
                    this.is_loading = false;
                }
            }
        },
        computed: {
            repositories_permissions:     () => gettext_provider.gettext("View repositories permissions"),
            repository_permissions_title: () => gettext_provider.gettext("Repository permissions"),
            hasError()                    { return this.error !== null; },
            displayButtonLoadAll()        { return ! this.is_loaded && ! this.is_loading },
        }
    };
</script>)
