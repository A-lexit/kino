<template>
    <div class="row">
        <div class="col-12 pt-5">
            <div class="d-flex align-items-center gap-3 text-body">
                <views-component></views-component>
                <likes-component></likes-component>
            </div>
        </div>
    </div>
</template>

<script>
import ViewsComponent from "./ViewsComponent.vue";
import LikesComponent from "./LikesComponent.vue";

export default {
    components: {
        ViewsComponent,
        LikesComponent
    },

    computed: {
        film() {
            return this.$store.state.film;
        }
    },

    mounted() {
        console.log('Component film mounted.');
    },

    created() {
        let url = window.location.pathname;
        let slug = url.substring(url.lastIndexOf('/') + 1);

        this.$store.commit('SET_SLUG', slug);
        this.$store.dispatch("getFilmData", slug);
        this.$store.dispatch("viewsIncrement", slug);
    }
}
</script>

<style scoped>

</style>

