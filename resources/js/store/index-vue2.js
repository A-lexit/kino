import { createApp } from 'vue'
import Vuex from 'vuex'

createApp().use(Vuex)

export default new Vuex.Store({
    state: {
        film: {
            comments: [],
            statistic: {
                likes: 0,
                views: 0
            }
        },
        slug: '',
        likeIt: true,
        commentSuccess: false,
        errors: []
    },
    actions: {
        getFilmData(context, payload) {
            axios.get('/api/film-json', {params: {slug:payload}}).then((response)=>{
                context.commit('SET_FILM', response.data.data);
            }).catch(()=>{
                console.log('Error');
            });
        },
        viewsIncrement(context, payload) {
            setTimeout(() => {
                axios.put('/api/film-views-increment', {slug:payload}).then((response) => {
                    context.commit('SET_FILM', response.data.data);
                }).catch(() =>{
                    console.log('Ошибка');
                });
            }, 5000)
        },
        addLike(context, payload) {
                axios.put('/api/film-likes-increment', {slug:payload.slug, increment:payload.increment }).then((response) =>{
                    context.commit('SET_FILM', response.data.data);
                    context.commit('SET_LIKE', !context.state.likeIt)
                }).catch(() =>{
                    console.log('Ошибка addLike');
                });
            console.log("После клика по кнопке", context.state.likeIt);
        },
        addComment(context, payload){
            axios.post('/api/film-add-comment', { subject:payload.subject, body:payload.body, film_id:payload.film_id, user_id:payload.user_id}).then((response) =>{
                context.commit('SET_COMMENT_SUCCESS', !context.state.commentSuccess);
                context.dispatch('getFilmData', context.state.slug)
            }).catch((error)=>{
                if(error.response.status === 422) {
                    context.state.errors = error.response.data.errors;
                }
            });
        }
    },

    getters: {
        filmViews(state) {
            return state.film.statistic.views;
        },

        filmLikes(state) {
            return state.film.statistic.likes;
        }
    },
    mutations: {
        SET_FILM(state, payload) {
            state.film = { ...state.film, ...payload };
        },
        SET_SLUG(state, payload) {
            return state.slug = payload;
        },
        SET_LIKE(state, payload) {
            return state.likeIt = payload;
        },
        SET_COMMENT_SUCCESS(state, payload) {
            state.commentSuccess = payload;
        }
    }
})
