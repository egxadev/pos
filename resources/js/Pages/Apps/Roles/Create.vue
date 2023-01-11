<template>

    <Head>
        <title>Add New Role - Aplikasi Kasir</title>
    </Head>
    <main class="c-main">
        <div class="container-fluid">
            <div class="fade-in">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card border-0 rounded-3 shadow border-top-purple">
                            <div class="card-header">
                                <span class="font-weight-bold">
                                    <i class="fa fa-shield-alt"></i> ADD ROLE
                                </span>
                            </div>
                            <div class="card-body">
                                <form @submit.prevent="submit">
                                    <div class="mb-3">
                                        <label class="fw-bold">Role Name</label>
                                        <input class="form-control" v-model="form.name"
                                            v-bind:class="{ 'is-invalid': errors.name }" type="text"
                                            placeholder="Role Name" />

                                        <div v-if="errors.name" class="alert alert-danger">
                                            {{ errors.name }}
                                        </div>
                                    </div>

                                    <hr />
                                    <div class="mb-3">
                                        <label class="fw-bold">Permissions</label>
                                        <br />
                                        <div class="form-check form-check-inline"
                                            v-for="(permission, index) in permissions" v-bind:key="index">
                                            <input class="form-check-input" type="checkbox" v-model="form.permissions"
                                                v-bind:value="permission.name" v-bind:id="`check-${permission.id}`" />
                                            <label class="form-check-label" v-bind:for="`check-${permission.id}`">{{
                                                permission.name
                                            }}</label>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-12">
                                            <button class="btn btn-primary shadow-sm rounded-sm" type="submit">
                                                SAVE
                                            </button>
                                            <button class="btn btn-warning shadow-sm rounded-sm ms-3" type="reset">
                                                RESET
                                            </button>
                                        </div>
                                    </div>
                                </form>
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
import { Head, Link } from "@inertiajs/inertia-vue3";
import { reactive } from "vue";
import { Inertia } from "@inertiajs/inertia";
import Swal from "sweetalert2";

export default {
    // LAYOUT
    layout: LayoutApp,

    // COMPONENTS
    components: {
        Head,
        Link,
    },

    // PROPS
    props: {
        errors: Object,
        permissions: Array,
    },

    setup() {
        // DEFINE STATE FORM
        const form = reactive({
            name: "",
            permissions: [],
        });

        // DEFINE METHOD "SUBMIT"
        const submit = () => {
            // SEND DATA TO SERVER
            Inertia.post("/apps/roles", {
                // DATA
                name: form.name,
                permissions: form.permissions,
            }, {
                onSuccess: () => {
                    // SHOW SUCCESS ALERT
                    Swal.fire({
                        title: "Success!",
                        text: "Role saved successfully.",
                        icon: "success",
                        showConfirmButton: false,
                        timer: 2000,
                    });
                },
            }
            );
        };

        return {
            form,
            submit,
        };
    },
};
</script>

<style>

</style>
