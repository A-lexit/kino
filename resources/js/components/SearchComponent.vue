<template>
    <div class="search-live position-relative">

<!--        <form
            method="get"
            action="/search"
            @submit="onSubmit"
            class="d-flex">

            <input
                type="text"
                class="form-control rounded-start-pill"
                name="s"
                v-model="query"
                @input="onInput"
                @focus="showDropdown = results.length > 0"
                placeholder="Пошук..."
                autocomplete="off"
            >

            <button
                class="btn btn-primary rounded-end-pill px-3"
                type="submit">

                <i class="bi bi-search"></i>

            </button>

        </form>-->


        <form
            method="get"
            action="/search"
            @submit="onSubmit"
            class="d-flex">

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

import {ref,onMounted,getCurrentInstance} from 'vue';

const query = ref('');

const results = ref([]);

const loading = ref(false);

const showDropdown = ref(false);

let debounceTimer = null;



function onInput()
{
    showDropdown.value = true;

    clearTimeout(debounceTimer);

    if(query.value.trim().length < 2)
    {
        results.value=[];

        return;
    }

    debounceTimer = setTimeout(search,300);
}



async function search()
{
    loading.value=true;

    try
    {
        const response = await fetch(
            `/search-suggestions?q=${encodeURIComponent(query.value)}`
        );

        results.value = await response.json();
    }
    catch(e)
    {
        results.value=[];
    }
    finally
    {
        loading.value=false;
    }
}



function onSubmit()
{
    // fallback без JS
}



onMounted(() => {

    const instance = getCurrentInstance();

    const root = instance.vnode.el;

    document.addEventListener('click',(e)=>{

        if(!root.contains(e.target))
        {
            showDropdown.value=false;
        }

    });

});

</script>

<style scoped>

.dropdown-menu{

    max-height:420px;

    overflow-y:auto;

    border-radius:.8rem;

    z-index:1055;

}

.dropdown-item img{

    object-fit:cover;

}

.dropdown-item:hover{

    background:#f8f9fa;

}







    .search-input{
        border-radius:0;
        border:1px solid #ced4da;
        border-right:none;
        height:46px;
        box-shadow:none;
    }

    .search-input:focus{
        border-color:#222;
        box-shadow:none;
    }

    .search-btn{
        width:56px;
        height:46px;
        border-radius:0;
        background:#212529;
        border:1px solid #212529;
        color:#fff;
        transition:.2s;
    }

    /*.search-btn:hover{
        background:#ff5604;
        border-color:#ff5604;
        color:#fff;
    }*/

    .search-btn:focus{
        box-shadow:none;
    }



















</style>
