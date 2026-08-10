<template>
    <div class="search-live position-relative">
        <form
            method="get"
            action="/search"
            @submit="onSubmit">

            <div class="input-group">
                <input
                    type="text"
                    class="form-control search-input"
                    name="s"
                    v-model="query"
                    @input="onInput"
                    @focus="showDropdown = results.length > 0"
                    placeholder="Пошук..."
                    autocomplete="off">

                <button
                    class="btn search-btn"
                    type="submit">
                    <i class="bi bi-search"></i>
                </button>
            </div>
        </form>

        <div
            v-if="showDropdown && query.trim().length >= 2"
            class="dropdown-menu show w-100 mt-2 shadow border-0">

            <div
                v-if="loading"
                class="dropdown-item text-center text-muted">
                Пошук...
            </div>

            <template v-else>
                <template v-if="results.length">
                    <a
                        v-for="item in results"
                        :key="item.url"
                        :href="item.url"
                        class="dropdown-item d-flex align-items-center gap-2">
                        <img
                            :src="item.image"
                            class="rounded flex-shrink-0"
                            width="42"
                            height="60"
                            style="object-fit:cover"
                            alt="">
                        <span class="small">
                            {{ item.title }}
                        </span>
                    </a>

                    <div class="dropdown-divider"></div>

                    <a
                        class="dropdown-item text-center fw-semibold text-primary"
                        :href="'/search?s=' + encodeURIComponent(query)">
                        Всі результати →
                    </a>
                </template>

                <div
                    v-else
                    class="dropdown-item text-center text-muted">
                    Нічого не знайдено
                </div>
            </template>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, getCurrentInstance } from 'vue';

const query = ref('');
const results = ref([]);
const loading = ref(false);
const showDropdown = ref(false);
let debounceTimer = null;

function onInput() {
    showDropdown.value = true;
    clearTimeout(debounceTimer);

    if (query.value.trim().length < 2) {
        results.value = [];
        return;
    }

    debounceTimer = setTimeout(search, 300);
}

async function search() {
    loading.value = true;
    try {
        const response = await fetch(
            `/search-suggestions?q=${encodeURIComponent(query.value)}`
        );
        results.value = await response.json();
    } catch (e) {
        results.value = [];
    } finally {
        loading.value = false;
    }
}

function onSubmit() {
    // fallback без JS
}

onMounted(() => {
    const instance = getCurrentInstance();
    const root = instance.vnode.el;

    document.addEventListener('click', (e) => {
        if (!root.contains(e.target)) {
            showDropdown.value = false;
        }
    });
});
</script>

<!--<style scoped>
.dropdown-menu {
    max-height: 420px;
    overflow-y: auto;
    border-radius: .8rem;
    z-index: 1055;
}

.dropdown-item img {
    object-fit: cover;
}

.dropdown-item:hover {
    background: #f8f9fa;
}

/* Біла обводка навколо всієї групи */
/* М'яка, спокійна напівпрозора рамка */
.input-group {
    border: 1px solid rgba(255, 255, 255, 0.15);
    border-radius: 6px;
    overflow: hidden;
    transition: border-color 0.2s ease;
}

/* Легкий акцент при фокусі */
.input-group:focus-within {
    border-color: rgba(255, 255, 255, 0.35);
}

.search-input {
    border: none !important;
    height: 46px;
    box-shadow: none !important;
    border-radius: 0 !important;
}

.search-input:focus {
    background-color: #fff;
}

.search-btn {
    width: 56px;
    height: 46px;
    border: none !important;
    border-radius: 0 !important;
    background: #212529;
    color: #fff;
    transition: .2s;
}

.search-btn:focus {
    box-shadow: none !important;
}
</style>-->
<style scoped>
.dropdown-menu {
    max-height: 420px;
    overflow-y: auto;
    border-radius: .8rem;
    z-index: 1055;
}

.dropdown-item img {
    object-fit: cover;
}

:global([data-bs-theme="dark"]) .dropdown-item:hover {
    background-color: var(--color-bg-soft, #2b303a);
}

/* Адаптивна рамка навколо всієї групи через системні змінні Bootstrap */
.input-group {
    border: 1px solid var(--bs-border-color, #dee2e6);
    border-radius: 6px;
    overflow: hidden;
    transition: border-color 0.2s ease;
}

/* При фокусі у світлій темі — м'який сірий, у темній — легкий акцент */
.input-group:focus-within {
    border-color: #adb5bd;
}

:global([data-bs-theme="dark"]) .input-group {
    border-color: var(--color-border-dark, rgba(255, 255, 255, 0.15));
}

:global([data-bs-theme="dark"]) .input-group:focus-within {
    border-color: rgba(255, 255, 255, 0.35);
}

.search-input {
    border: none !important;
    height: 46px;
    box-shadow: none !important;
    border-radius: 0 !important;
    background-color: transparent;
}

.search-input:focus {
    background-color: transparent;
    box-shadow: none !important;
}

.search-btn {
    width: 56px;
    height: 46px;
    border: none !important;
    border-radius: 0 !important;
    background: #212529;
    color: #fff;
    transition: .2s;
}

.search-btn:focus {
    box-shadow: none !important;
}
</style>
