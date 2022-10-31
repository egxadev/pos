<template>
    <nav>
        <ul v-bind:class="`pagination justify-content-${align} mb-0`">
            <li v-bind:class="['page-item', link.url == null ? 'disabled' : '', link.active ? 'active' : '']" v-for="(link, index) in links" v-bind:key="index">
                <Link class="page-link" v-bind:href="link.url === null ? '#' : link.url" v-html="link.label">
                </Link>
            </li>
        </ul>
    </nav>
</template>
<script>
// IMPORT LINK
import { Link } from '@inertiajs/inertia-vue3';

export default {
    // PROPS
    props: {
        links: Array,
        align: String,
    },

    // COMPONENTS
    components: {
        Link,
    },
};
</script>
