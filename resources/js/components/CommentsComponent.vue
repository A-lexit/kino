<template>
    <div class="row">
        <form @submit.prevent="submit_form()" v-if="!commentSuccess">

            <div class="mb-3 mt-4">
                <h5>
                    Коментар
                    <small class="text-secondary fs-6">
                        (тільки для зареєстрованих користувачів)
                    </small>
                </h5>

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

            <button class="btn btn-primary" type="submit">Надіслати</button>
        </form>

        <div class="alert alert-success" role="alert" v-else>
            Коментар успішно надіслано!
        </div>

        <div class="pb-2 mt-4 mx-auto" style="min-width: 100%;" v-for="comment in comments" :key="comment.id">
            <!-- v-if перенесено сюди -->
            <div class="comment-card rounded overflow-hidden" style="min-width: 100%;" v-if="comment.status == '1'">
                <div class="toast-header">
                    <img src="https://placehold.co/50/5F113B/FFFFFF?text=User" class="rounded me-2" alt="...">
                    <strong class="me-auto">{{comment.subject}}</strong>
                    <small class="text-muted">{{comment.created_at}}</small>
                </div>
                <div class="toast-body">
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
/* 1. ОСНОВНІ СТИЛІ ПОЛЯ ВВОДУ */
/* Поле коментаря */
/* Поле коментаря */
textarea#commentBody.form-control {
    border-width: 3px !important;
    border-style: solid !important;
    transition: border-color 0.2s ease !important;
}

/* Темна тема */
:global([data-bs-theme="dark"]) textarea#commentBody.form-control {
    background-color: var(--color-bg-soft, #1a1d24) !important;
    color: var(--color-text, #e1e3e8) !important;
    border-color: #3a4150 !important;

    --bs-focus-ring-color: transparent !important;
}

:global([data-bs-theme="dark"]) textarea#commentBody.form-control:hover {
    border-color: #454c5a !important;
}

:global([data-bs-theme="dark"]) textarea#commentBody.form-control:focus,
:global([data-bs-theme="dark"]) textarea#commentBody.form-control:focus-visible {
    border-color: #daff00 !important;
    box-shadow: none !important;
    outline: none !important;
    --bs-focus-ring-color: transparent !important;
}

/* 3. КАРТКИ КОМЕНТАРІВ */

/* Світла тема: тонка, ледь помітна рамка */
.comment-card {
    border: 1px solid rgba(0, 0, 0, 0.08) !important;
}

/* Темна тема: зберігаємо темну рамку */
:global([data-bs-theme="dark"]) .comment-card {
    border: 1px solid #3a4150 !important;
}

:global([data-bs-theme="dark"]) .toast-header {
    background-color: var(--color-bg-section-alt, #222730) !important;
    color: var(--color-text, #e1e3e8) !important;
    border-bottom: 1px solid #3a4150 !important;
}

:global([data-bs-theme="dark"]) .toast-body {
    background-color: var(--color-bg-section, #1c2026) !important;
    color: var(--color-text, #e1e3e8) !important;
    border: none !important;
}
</style>

