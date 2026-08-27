import './bootstrap';
import { createApp } from 'vue';


import store from './store';


import FilmComponent from './components/FilmComponent.vue';
import ViewsComponent from './components/ViewsComponent.vue';
import LikesComponent from './components/LikesComponent.vue';
import CommentsComponent from './components/CommentsComponent.vue';
import SearchComponent from './components/SearchComponent.vue';

const app = createApp({});

const searchApp = createApp({});


// Реєстрація компонентів
app.component('film-component', FilmComponent);
app.component('views-component', ViewsComponent);
app.component('likes-component', LikesComponent);
app.component('comments-component', CommentsComponent);
searchApp.component('search-component', SearchComponent);

// Підключення Vuex (або Pinia)
app.use(store);


// Монтуємо Vue лише якщо контейнер існує
if (document.getElementById('app')) {
    console.log('3. Елемент #app знайдено в HTML, монтуємо Vue...');
    app.mount('#app');
} else {
}

// Монтуємо компонент пошуку
if (document.getElementById('search-app')) {
    searchApp.mount('#search-app');
}
