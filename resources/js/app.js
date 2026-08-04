import './bootstrap';
import { createApp } from 'vue';

console.log('1. JS файл app.js почав роботу'); // <-- Додати для перевірки

import store from './store'; // Переконайтеся, що шлях правильний

// Імпорт компонентів
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

// Перевірка HTML-елемента перед монтуванням
/*if (document.getElementById('app')) {
    console.log('3. Елемент #app знайдено в HTML, монтуємо Vue...');
} else {
    console.error('3. ПОМИЛКА: Елемент з id="app" НЕ знайдено в HTML шаблоні сторінки!');
}

if (document.getElementById('search-app')) {
    searchApp.mount('#search-app');
}*/

// Монтуємо Vue лише якщо контейнер існує
if (document.getElementById('app')) {
    console.log('3. Елемент #app знайдено в HTML, монтуємо Vue...');
    app.mount('#app');
} else {
    /*console.warn('3. Елемент #app не знайдено на цій сторінці.');*/
}

// Монтуємо компонент пошуку
if (document.getElementById('search-app')) {
    searchApp.mount('#search-app');
}



// Монтування
/*app.mount('#app');*/
