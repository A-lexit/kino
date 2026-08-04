<template>
    <span @click="addLike()"
          class="badge likesButton"
          :class="isLike ? 'bg-primary' : 'bg-danger'">
    <i class="bi bi-hand-thumbs-up me-1"></i>
    {{ likesNumber }}
</span>

</template>

<script>

export default {
    computed: {
        isLike() {
            return this.$store.state.likeIt;
        },
        likesNumber() {
            return this.$store.getters.filmLikes;
        },
        /*...mapGetters('film', ['filmLikes']),*/
    },
    methods: {
        addLike(){
            this.$store.dispatch('addLike', {
                slug : this.$store.state.slug,
                increment: this.isLike
            })
        }
    },
    mounted() {
        console.log('Component mounted.')
    }
}
</script>

<style scoped>

.likesButton {
    cursor: pointer;
}

</style>
