<template>
    <div class="fill-height" v-resize="onResize">
        <div v-if="error_load" class="fill-height pa-5">
            <v-row class="fill-height align-content-center justify-center">
                <v-col cols="1" style="min-width: 100px; max-width: 600px;" class="flex-grow-1 flex-shrink-0 pa-0 justify-space-between">
                    <v-card class="ma-5" outlined shaped elevation="0" width="600">
                        <v-alert text type="error">Ошибка загрузки</v-alert>
                        <v-card-actions class="mb-5">
                            <v-row justify="center" no-gutters>
                                <v-btn depressed color="error" @click="perehod('/forms')" class="mx-2">
                                    <v-icon>mdi-check</v-icon>
                                    ОК
                                </v-btn>
                            </v-row>
                        </v-card-actions>
                    </v-card>
                </v-col>
            </v-row>
        </div>
        <div v-if="load" class="fill-height pa-5">
            <v-row class="fill-height align-content-center justify-center">
                <v-col class="subtitle-1 text-center" cols="12">Идет загрузка данных. Подождите...</v-col>
                <v-col cols="1" style="min-width: 100px; max-width: 600px;" class="flex-grow-1 flex-shrink-0 pa-0 justify-space-between">
                    <v-progress-linear color="blue-grey darken-5" indeterminate rounded height="6"></v-progress-linear>
                </v-col>
            </v-row>
        </div>
        <div v-if="((!error_load) && (!load))" class="fill-height">
            <v-toolbar class="blue-grey darken-5 white--text headline subtitle-1" dark>
                <v-list-item class="px-4">
                    <v-list-item-action>
                        <v-btn outlined @click="perehod('/forms')">К формам
                            <v-icon>mdi-door-open</v-icon>
                        </v-btn>
                    </v-list-item-action>
                    <v-list-item-content>
                        <v-list-item-title class="text-center title">Просмотр результата формы</v-list-item-title>
                    </v-list-item-content>
                </v-list-item>
            </v-toolbar>
            <v-data-table class="elevation-1 mx-3" v-model="selected" light :headers="headers" :items="data" :options.sync="options" :server-items-length="totallengtdata"
                :loading="loading" item-key="id" :page.sync="page" :items-per-page="itemsPerPage" :single-select="singleSelect"
                :footer-props="{
                    showFirstLastPage: true,
                    showCurrentPage: true,
                    itemsPerPageOptions: [10,15,20],
                    firstIcon: 'mdi-chevron-double-left',
                    lastIcon: 'mdi-chevron-double-right',
                    prevIcon: 'mdi-chevron-left',
                    nextIcon: 'mdi-chevron-right',
                }"
            >
                <template v-slot:no-data>
                    Не найдено подходящих записей
                </template>
                <template v-slot:top>
                    <v-toolbar flat color="white" class="px-4 mb-4">
                        <v-toolbar-title>Список</v-toolbar-title>
                        <v-tooltip top>
                            <template v-slot:activator="{ on }">
                                <v-btn icon v-on="on" @click="init_view_result()"><v-icon>mdi-autorenew</v-icon></v-btn>
                            </template>
                            <span>Обновить</span>
                        </v-tooltip>
                        <v-divider class="mx-4" inset vertical></v-divider>
                        <div class="flex-grow-1"></div>
                        <v-text-field class="mt-8" v-model="search" outlined append-icon="search" label="Поиск" single-line clearable maxlength="50" counter @input="changeSearch"></v-text-field>
                    </v-toolbar>
                </template>
            </v-data-table>
        </div>
        <div>
            <v-snackbar v-model="snackbar" :color="color_s" :timeout="timeout">
                {{ text }}
                <v-btn color="white" text @click="snackbar = false">Закрыть</v-btn>
            </v-snackbar>
        </div>
    </div>
</template>

<script>

export default
{
    components: {

    },
    props: {
        mainpath: String,
        id: {
            type: [String, Number],
            default: -1,
        },
    },
    data: () => ({
        selection: null,
        form_scheme: null,
        windowSize: { x: 0, y: 0, },
        heightBlock: 0,
        heightBlock1: 0,
        widthBlockDiv: 0,
        snackbar: false,
        text: 'My timeout is set to 10000.',
        timeout: 10000,
        color_s: "success",
        final_save: false,
        number_query: 0,
        load: true,
        otvet_load: null,
        error_load: false,
        data: [],
        timer: null,
        cancelSource: null,
        request: null,
        search: '',
        page: 1,
        pageCount: 1,
        itemsPerPage: 10,
        selected: [],
        headers: [],
        options: {},
        totallengtdata: 0,
        loading: false,
        singleSelect: false,
    }),
    computed: {

    },
    watch: {
        options: {
            handler () {
                this.init_view_result();
            },
            deep: true,
        },
    },
    created () {
        let id = + this.id;
        if (id > 0) {
            this.init_view_result();
        }
        else {
            this.error_load = true;
            this.load = false;
        }
    },
    methods: {
        init() {
            this.onResize();
        },
        changeSearch() {
            this.clearTimer();
            if (this.search && this.search.length > 0) {
                this.timer = setTimeout(() => {
                    this.init_view_result();
                }, 700)
            } else {
                this.init_view_result();
            }
        },
        clearTimer() {
            if (this.timer) {
                clearTimeout(this.timer);
            }
        },
        init_view_result() {
            this.selected = [];
            this.loading = true;

            let { sortBy, sortDesc, page, itemsPerPage } = this.options;
            let search = this.search;
            if(typeof page == "undefined") {
                page = 1;
                itemsPerPage = 10;
                sortBy = '';
                sortDesc = '';
            }
            if (!search) {
                search = '';
            }

            let formData = new FormData();
            let id = this.id;
            formData.append('id', id);
            formData.append('page', page);
            formData.append('itemsPerPage', itemsPerPage);
            formData.append('sortBy', sortBy);
            formData.append('sortDesc', sortDesc);
            formData.append('search', search);

            let href_forms = this.mainpath + '/forms/get_result_form';

            this.cancel();
            let axiosSource = axios.CancelToken.source();
            this.request = { cancel: axiosSource.cancel };

            axios.post(
                href_forms,
                formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    },
                    cancelToken: axiosSource.token,
                }
            ).then((response) => {
                this.loading = false;
                this.otvet_load = response.data;
                this.headers = this.otvet_load.headers;
                this.data = this.otvet_load.items;
                this.totallengtdata = this.otvet_load.data.total;
                this.pageCount = this.otvet_load.last_page;
                this.page = page;
                this.error_load = false;
                this.load = false;
            })
            .catch(
                function(thrown) {
                    if (axios.isCancel(thrown)) {
                        console.log('Request canceled', thrown.message);
                    } else {
                        this.error_load = true;
                        this.load = false;
                    }
                }
            );
        },
        cancel() {
            if (this.request) {
                this.request.cancel();
            }
        },
        updateState() {
            this.request = null;
        },
        onResize() {
            this.windowSize = { x: window.innerWidth, y: window.innerHeight };
            this.heightBlock = this.windowSize.y - 140;
            this.widthBlockDiv = this.windowSize.x * 0.8;
            this.heightBlock1 = this.windowSize.y - 240;
        },
        perehod (href) {
            if (href != this.$route.path) {
                this.$router.push(href);
            }
        },
    },
}
</script>