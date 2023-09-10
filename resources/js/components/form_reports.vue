<template>
<div>
    <div class="text-center pt-2" style="padding:5px">
        <v-pagination total-visible="10" color="cyan darken-3" v-model="page" :length="pageCount"></v-pagination>
    </div>
    <div>
        <v-toolbar class="blue-grey darken-5 white--text headline subtitle-1 px-4" dark>
            <v-spacer></v-spacer>
            <v-list-item class="pa-0">
                <v-list-item-content>
                    <v-list-item-title class="text-center title">Отчеты</v-list-item-title>
                </v-list-item-content>
            </v-list-item>
        </v-toolbar>
        <v-data-table class="elevation-1 mx-3" v-model="selected" light :headers="headers" :items="data" :options.sync="options" :server-items-length="totallengtdata"
            :loading="loading" item-key="id" :page.sync="page"
            :items-per-page="itemsPerPage" :single-select="singleSelect" :header-props="{sortIcon:'mdi-chevron-up'}"
            :footer-props="{
                showFirstLastPage: true,
                showCurrentPage: true,
                itemsPerPageOptions: [10,15,20],
                firstIcon: 'mdi-chevron-double-left',
                lastIcon: 'mdi-chevron-double-right',
                prevIcon: 'mdi-chevron-left',
                nextIcon: 'mdi-chevron-right',
            }">
            <template v-slot:no-data>
                Не найдено подходящих записей
            </template>
            <template v-slot:top>
                <v-toolbar flat color="white" class="px-4">
                    <v-toolbar-title>Список</v-toolbar-title>
                    <v-tooltip top>
                        <template v-slot:activator="{ on }">
                            <v-btn icon v-on="on" @click="init_forms()">
                                <v-icon>mdi-autorenew</v-icon>
                            </v-btn>
                        </template>
                        <span>Обновить</span>
                    </v-tooltip>
                    <v-divider class="mx-4" inset vertical></v-divider>
                    <div class="flex-grow-1"></div>
                    <v-text-field class="mt-8" v-model="search" outlined append-icon="search" label="Поиск" single-line clearable maxlength="50" counter @input="changeSearch"></v-text-field>
                </v-toolbar>
            </template>
            <template v-slot:item.actions="{ item }">
                <v-tooltip top>
                    <template v-slot:activator="{ on }">
                        <v-btn class="mx-2" icon v-on="on" @click="editItem(item)">
                            <v-icon color="blue-grey">mdi-file-sign</v-icon>
                        </v-btn>
                    </template>
                    <span>Заполнить форму</span>
                </v-tooltip>
            </template>
        </v-data-table>
    </div>
    <div class="text-center mb-12 pt-2" style="padding:5px">
        <v-pagination total-visible="10" color="cyan darken-3" v-model="page" :length="pageCount"></v-pagination>
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
import moment from 'moment';
export default {
    components: {

    },
    props: {
        mainpath: String,
    },
    data: () => ({
        dis_notify_icon: false,
        data: [],
        timer: null,
        cancelSource: null,
        request: null,
        search: '',
        page: 1,
        pageCount: 1,
        itemsPerPage: 10,
        selected: [],
        headers: [
            { text: '№ п/п', align: 'left', width: '70px', value: 'id', class: 'px-2', },
            { text: 'Наименование', align: 'left', width: '70px', value: 'name', class: 'px-2', },
            { text: '', align: 'right', sortable: false, width: '40px', value: 'actions', },
        ],
        options: {},
        totallengtdata: 0,
        loading: false,
        singleSelect: false,
        writer: false,
        id_delete: 0,
        snackbar: false,
        text: 'My timeout is set to 10000.',
        timeout: 10000,
        color_s: "success",
        dialog_del_vib: false,
        otvet_del: null,
        new_item: false,
        edit_item: false,
        itemZap: {},
        itemTemplate: {
            id: 0,
            data_vin: null,
            snils: '',
            fam: '',
            im: '',
            otch: '',
            address: '',
            prich_prist: 1,
            data_vin_vosst: null,
            struct_id: 0,
            prim: '',
        },
        fullscreen: true,
        dialog_export_excel: false,
        formir: false,
    }),
    computed: {

    },
    watch: {
        options: {
            handler () {
                this.init_forms();
            },
            deep: true,
        },
    },
    created() {
        this.init()
    },
    methods: {
        format_date_ru(item) {
            var m = moment(item);
            if (m.isValid()) return m.format('DD.MM.YYYY');
            return null;
        },
        init() {

        },
        changeSearch() {
            this.clearTimer();
            if (this.search && this.search.length > 0) {
                this.timer = setTimeout(() => {
                    this.init_forms();
                }, 700)
            } else {
                this.init_forms();
            }
        },
        clearTimer() {
            if (this.timer) {
                clearTimeout(this.timer);
            }
        },
        init_forms() {
            this.selected = [];
            this.loading = true;
            let { sortBy, sortDesc, page, itemsPerPage } = this.options;
            let search = this.search;

            if (typeof page == "undefined") {
                page = 1;
                itemsPerPage = 10;
                sortBy = '';
                sortDesc = '';
                search = '';
            }
            if (!search) {
                search = '';
            }

            let formData = new FormData();
            formData.append('page', page);
            formData.append('itemsPerPage', itemsPerPage);
            formData.append('sortBy', sortBy);
            formData.append('sortDesc', sortDesc);
            formData.append('search', search);

            let href_forms = this.mainpath + '/forms/get_forms';

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
                this.data = response.data.data;
                this.totallengtdata = response.data.total;
                this.pageCount = response.data.last_page;
                this.page = page;
                this.updateState();
            })
            .catch(
                function(thrown) {
                    if (axios.isCancel(thrown)) {
                        console.log('Request canceled', thrown.message);
                    } else {

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
        editItem (item) {
            let href = "/forms/fill_form?id="+item.id
            if (href != this.$route.path) {
                this.$router.push(href);
            }
        },
    },
}
</script>