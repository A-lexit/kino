<template>
    <div class="row">
        <form @submit.prevent="submit_form()" v-if="!commentSuccess">
<!--            <div class="mb-3">
                <label for="commentSubject" class="form-label">Ваше ім'я</label>
                <input type="text" class="form-control" id="commentSubject" v-model="subject">
                <div class="alert alert-warning" role="alert" v-if="errorsMessage.subject">
                    {{errorsMessage.subject[0]}}
                </div>
            </div>-->

            <div class="mb-3">

                <label for="commentBody" class="h5 form-label">
                    Коментар
                    <small class="text-secondary">
                        (тільки для зареєстрованих користувачів)
                    </small>
                </label>

                <textarea
                    class="form-control"
                    id="commentBody"
                    rows="3"
                    v-model="body">
                </textarea>

                <div
                    class="alert alert-warning mt-2"
                    role="alert"
                    v-if="errorsMessage.body">
                    {{ errorsMessage.body[0] }}
                </div>

            </div>

            <button class="btn btn-dark" type="submit">Надіслати</button>
        </form>
        <div class="alert alert-success" role="alert" v-else>
            Комментарий успешно отправлен!
        </div>

        <div class="pb-2 mt-5 mx-auto" style="min-width: 100%; " v-for="comment in comments" >
            <div class="" style="min-width: 100%;">
                <div class="toast-header" v-if="comment.status=='1'">
                    <img src="https://placehold.co/50/5F113B/FFFFFF?text=User" class="rounded me-2" alt="...">
                    <strong class="me-auto" >{{comment.subject}}</strong>
                    <small  class="text-muted"  >{{comment.created_at}}</small>

                </div>
                <div class="toast-body " v-if="comment.status=='1'">
                    {{comment.body}}
                </div>
            </div>
          </div>
        </div>
</template>

<script>

export default {
    data() {
        return {
            subject: '',
            body: ''
        }
    },
    computed: {
        comments() {
            return this.$store.state.film.comments;
        },
        commentSuccess(){
            return this.$store.state.commentSuccess;
        },
        errorsMessage(){
            return this.$store.state.errors;
        },
    },
    methods: {
        submit_form(){
            this.$store.dispatch('addComment', {
                subject: this.subject,
                body: this.body,
                status: this.status,
                film_id : this.$store.state.film.id,
            })
        }
    },
    mounted() {
        console.log('Component mounted.')
    }
}
</script>

<style scoped>

</style>
