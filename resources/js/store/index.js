import { createStore } from 'vuex';

export default createStore({
    state: {
        film: {
            comments: [],
            statistic: {
                likes: 0,
                views: 0,
            },
        },

        slug: '',
        likeIt: true,
        commentSuccess: false,
        errors: [],
    },

    getters: {
        filmViews: (state) => state.film.statistic.views,

        filmLikes: (state) => state.film.statistic.likes,
    },

    mutations: {
        SET_FILM(state, payload) {
            state.film = {
                ...state.film,
                ...payload,
            };
        },

        SET_SLUG(state, payload) {
            state.slug = payload;
        },

        SET_LIKE(state, payload) {
            state.likeIt = payload;
        },

        SET_COMMENT_SUCCESS(state, payload) {
            state.commentSuccess = payload;
        },

        SET_ERRORS(state, payload) {
            state.errors = payload;
        },
    },

    actions: {
        async getFilmData({ commit }, slug) {
            try {
                const response = await axios.get('/api/film-json', {
                    params: {
                        slug,
                    },
                });

                commit('SET_FILM', response.data.data);
            } catch (error) {
                console.error(error);
            }
        },

        async viewsIncrement({ commit }, slug) {
            await new Promise((resolve) => setTimeout(resolve, 5000));

            try {
                const response = await axios.put('/api/film-views-increment', {
                    slug,
                });

                commit('SET_FILM', response.data.data);
            } catch (error) {
                console.error(error);
            }
        },

        async addLike({ commit, state }, payload) {
            try {
                const response = await axios.put('/api/film-likes-increment', {
                    slug: payload.slug,
                    increment: payload.increment,
                });

                commit('SET_FILM', response.data.data);
                commit('SET_LIKE', !state.likeIt);

                console.log('Після кліку:', state.likeIt);
            } catch (error) {
                console.error('Помилка addLike', error);
            }
        },

        async addComment({ commit, dispatch, state }, payload) {
            commit('SET_ERRORS', []);

            try {
                await axios.post('/api/film-add-comment', {
                    subject: payload.subject,
                    body: payload.body,
                    film_id: payload.film_id,
                    user_id: payload.user_id,
                });

                commit('SET_COMMENT_SUCCESS', !state.commentSuccess);

                await dispatch('getFilmData', state.slug);
            } catch (error) {
                if (error.response?.status === 422) {
                    commit('SET_ERRORS', error.response.data.errors);
                } else {
                    console.error(error);
                }
            }
        },
    },
});
