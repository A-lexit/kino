<template>
    <span class="badge bg-light text-dark border pe-none">
        <i class="bi bi-eye me-1"></i>
        {{ viewsNumber }}
    </span>
</template>

<script>
export default {
    computed: {
        viewsNumber() {
            return this.$store.getters.filmViews;
        }
    },
    mounted() {
        console.log('Component mounted.')
    }
}
</script>

<style scoped>

</style>
