<template>

    <Head>
        <title>Roles - Aplikasi Kasir</title>
    </Head>
    <main class="c-main">
        <div class="container-fluid">
            <div class="fade-in">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card border-0 rounded-3 shadow border-top-purple">
                            <div class="card-header">
                                <span class="font-weight-bold"><i class="fa fa-shield-alt"></i> ROLES</span>
                            </div>
                            <div class="card-body">
                                <form @submit.prevent="handleSearch">
                                    <div class="input-group mb-3">

                                        <Link href="/apps/roles/create" v-if="hasAnyPermission(['roles.create'])"
                                            class="btn btn-primary input-group-text"><i
                                            class="fa fa-plus-circle me-2"></i> NEW</Link>

                                        <input type="text" class="form-control" v-model="search"
                                            placeholder="search by role name...">

                                        <button class="btn btn-primary input-group-text" type="submit">
                                            <i class="fa fa-search me-2"></i> SEARCH
                                        </button>

                                    </div>
                                </form>
                                <div class="table-responsive">
                                    <table class="table table-responsive table-striped table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th scope="col">Role Name</th>
                                                <th scope="col" style="width:50%">Permissions</th>
                                                <th scope="col" style="width:20%">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(role, index) in roles.data" v-bind:key="index">
                                                <td>{{ role.name }}</td>
                                                <td>
                                                    <span v-for="(permission, index) in role.permissions"
                                                        v-bind:key="index"
                                                        class="badge badge-primary shadow border-0 ms-2 mb-2">
                                                        {{ permission.name }}
                                                    </span>
                                                </td>
                                                <td class="text-center">
                                                    <Link v-bind:href="`/apps/roles/${role.id}/edit`"
                                                        v-if="hasAnyPermission(['roles.edit'])"
                                                        class="btn btn-success btn-sm me-2"><i
                                                        class="fa fa-pencil-alt me-1"></i> EDIT</Link>
                                                    <button @click.prevent="destroy(role.id)"
                                                        v-if="hasAnyPermission(['roles.delete'])"
                                                        class="btn btn-danger btn-sm"><i class="fa fa-trash"></i>
                                                        DELETE</button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <Pagination v-bind:links="roles.links" align="end" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</template>

<script>
import LayoutApp from "../../../Layouts/App.vue";
import Pagination from "../../../Components/Pagination.vue";
import { Head, Link } from "@inertiajs/inertia-vue3";
import { ref } from "vue";
import { Inertia } from "@inertiajs/inertia";
import Swal from 'sweetalert2';

export default {
    // LAYOUT
    layout: LayoutApp,

    // COMPONENTS
    components: {
        Head,
        Link,
        Pagination,
    },

    // PROPS
    props: {
        roles: Object,
    },

    setup() {
        // DEFINE METHOD "SEARCH"
        const search = ref("" || new URL(document.location).searchParams.get("q"));

        // DEFINE METHOD SEARCH
        const handleSearch = () => {
            // SEND DATA TO SERVER
            Inertia.get("/apps/roles", {
                // SEND PARAMS "Q" WITH VALUE FROM STATE "search"
                q: search.value,
            });
        };

        // DEFINE METHOD "DESTROY"
        const destroy = (id) => {
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    Inertia.delete(`/apps/roles/${id}`);

                    Swal.fire({
                        title: 'Deleted!',
                        text: 'Role deleted successfully.',
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false,
                    })
                }
            })
        }

        return {
            search,
            handleSearch,
            destroy
        };
    },
};
</script>
