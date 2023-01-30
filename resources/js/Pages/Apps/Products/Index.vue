<template>

    <Head>
        <title>Products - Aplikasi Kasir</title>
    </Head>
    <main class="c-main">
        <div class="container-fluid">
            <div class="fade-in">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card border-0 rounded-3 shadow border-top-purple">
                            <div class="card-header">
                                <span class="font-weight-bold"><i class="fa fa-shopping-bag"></i> PRODUCTS</span>
                            </div>
                            <div class="card-body">
                                <form @submit.prevent="handleSearch">
                                    <div class="input-group mb-3">
                                        <Link href="/apps/products/create" v-if="hasAnyPermission(['products.create'])"
                                            class="btn btn-primary input-group-text"> <i
                                            class="fa fa-plus-circle me-2"></i> NEW</Link>
                                        <input type="text" class="form-control" v-model="search"
                                            placeholder="search by product title...">

                                        <button class="btn btn-primary input-group-text" type="submit"> <i
                                                class="fa fa-search me-2"></i> SEARCH</button>

                                    </div>
                                </form>
                                <table class="table table-responsive table-striped table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th scope="col">Barcode</th>
                                            <th scope="col">Title</th>
                                            <th scope="col">Buy Price</th>
                                            <th scope="col">Sell Price</th>
                                            <th scope="col">Stock</th>
                                            <th scope="col" style="width:20%">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(product, index) in products.data" v-bind:key="index">
                                            <td class="text-center">{{ product.barcode }}</td>
                                            <td>{{ product.title }}</td>
                                            <td>Rp. {{ formatPrice(product.buy_price) }}</td>
                                            <td>Rp. {{ formatPrice(product.sell_price) }}</td>
                                            <td>{{ product.stock }}</td>
                                            <td class="text-center">
                                                <Link v-bind:href="`/apps/products/${product.id}/edit`"
                                                    v-if="hasAnyPermission(['products.edit'])"
                                                    class="btn btn-success btn-sm me-2"><i
                                                    class="fa fa-pencil-alt me-1"></i> EDIT</Link>
                                                <button @click.prevent="destroy(product.id)"
                                                    v-if="hasAnyPermission(['products.delete'])"
                                                    class="btn btn-danger btn-sm"><i class="fa fa-trash"></i>
                                                    DELETE</button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <Pagination v-bind:links="products.links" align="end" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</template>

<script>
import LayoutApp from '../../../Layouts/App.vue';
import Pagination from '../../../Components/Pagination.vue';
import { Head, Link } from '@inertiajs/inertia-vue3';
import { Inertia } from '@inertiajs/inertia';
import { ref } from 'vue';
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
        products: Object,
    },

    setup() {
        // STATE
        const search = ref('' || (new URL(document.location)).searchParams.get('q'));

        // METHOD SEARCH
        const handleSearch = () => {
            Inertia.get('/apps/products', {
                // SEND PARAMS "Q" WITH VALUE FROM STATE "SEARCH"
                q: search.value,
            });
        }

        // METHOD DESTROY
        const destroy = (id) => {
            // ALERT CONFIRM
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            })
                .then((result) => {
                    if (result.isConfirmed) {

                        // SEND TO SERVER
                        Inertia.delete(`/apps/products/${id}`);

                        // SHOW ALERT SUCCESS
                        Swal.fire({
                            title: 'Deleted!',
                            text: 'Product deleted successfully.',
                            icon: 'success',
                            timer: 2000,
                            showConfirmButton: false,
                        });
                    }
                })
        }

        return {
            search,
            handleSearch,
            destroy,
        }
    }
}
</script>