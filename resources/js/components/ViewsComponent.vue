<template>
    <span class="badge views-badge border pe-none">
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
/* Стилі за замовчуванням (Світла тема) */
.views-badge {
    background-color: #f8f9fa;
    color: #212529;
    border-color: #dee2e6 !important;
}


:global([data-bs-theme="dark"]) .views-badge {
    background-color: var(--color-bg-section, #1c2026) !important;
    color: var(--color-text, #e1e3e8) !important;
    border-color: var(--color-border-dark, #3a4150) !important;
}

:global([data-bs-theme="dark"]) .views-badge i {
    color: var(--color-primary, #8ea0ff);
}
</style>
