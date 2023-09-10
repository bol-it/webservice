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
                    <v-list-item-title class="text-center title">Формы</v-list-item-title>
                </v-list-item-content>
                <v-list-item-action>
                    <v-btn outlined @click="new_zap('forms/new_forms')">Добавить
                        <v-icon>mdi-plus-box</v-icon>
                    </v-btn>
                </v-list-item-action>
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
                <v-toolbar flat color="white" class="px-4 mb-4">
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
                        <v-btn class="mx-2" icon v-on="on" color="blue-grey" @click="editItem(item)">
                            <v-icon>mdi-lead-pencil</v-icon>
                        </v-btn>
                    </template>
                    <span>Редактировать</span>
                </v-tooltip>
                <v-tooltip top>
                    <template v-slot:activator="{ on }">
                        <v-btn class="mx-2" icon v-on="on" color="red lighten-1" @click="deleteItem(item)">
                            <v-icon>mdi-delete-forever</v-icon>
                        </v-btn>
                    </template>
                    <span>Удалить</span>
                </v-tooltip>
                <v-tooltip top>
                    <template v-slot:activator="{ on }">
                    <v-btn  icon v-on="on" color="brown lighten-1" @click="viewItem(item.id)"><v-icon>mdi-eye</v-icon></v-btn>
                    </template>
                    <span>Просмотр результатов</span>
                </v-tooltip>
                <v-tooltip top>
                    <template v-slot:activator="{ on }">
                        <v-btn class="mx-2" icon v-on="on" color="green lighten-1" @click="saveExcel(item.id)">
                            <v-icon>mdi-file-excel</v-icon>
                        </v-btn>
                    </template>
                    <span>Сохранить в Excel</span>
                </v-tooltip>
                <v-tooltip top>
                    <template v-slot:activator="{ on }">
                        <v-btn class="mx-2" icon v-on="on" color="blue lighten-1" @click="saveWord(item.id)">
                            <v-icon>mdi-file-word</v-icon>
                        </v-btn>
                    </template>
                    <span>Сохранить в Word</span>
                </v-tooltip>
            </template>
        </v-data-table>
    </div>
    <div class="text-center mb-12 pt-2" style="padding:5px">
        <v-pagination total-visible="10" color="cyan darken-3" v-model="page" :length="pageCount"></v-pagination>
    </div>
    <div>
        <v-row justify="center">
            <v-dialog v-model="dialog_del_vib" persistent max-width="400">
                <v-card>
                    <v-card-title class="headline blue white--text" primary-title>
                        <v-icon dark large>mdi-information-outline</v-icon>
                        <v-spacer></v-spacer>
                        <strong>Внимание</strong>
                        <v-spacer></v-spacer>
                    </v-card-title>
                    <v-card-text class="pa-3 text-center"><strong>Вы собираетесь удалить форму?</strong></v-card-text>
                    <v-card-actions>
                        <div class="flex-grow-1"></div>
                        <div class="text-xs-center">
                            <v-btn color="error" @click="dialog_del_vib = false">
                                <v-icon>mdi-cancel</v-icon>Отмена
                            </v-btn>
                            <v-btn color="success" @click="delete_zap()">
                                <v-icon>mdi-check</v-icon>Удалить
                            </v-btn>
                        </div>
                        <div class="flex-grow-1"></div>
                    </v-card-actions>
                </v-card>
            </v-dialog>
        </v-row>
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
        deleteItem(item) {
            this.id_delete = item.id;
            this.dialog_del_vib = true;
        },
        delete_zap() {
            axios.post(this.mainpath + '/forms/delete_form', {
                id: this.id_delete,
            }).then((response) => {
                let otvet_del = response.data;
                if (otvet_del.error == 1) {
                    this.snackbar = true;
                    this.text = otvet_del.message;
                    this.color_s = "error";
                }
                if (otvet_del.success == 1) {
                    this.snackbar = true;
                    this.text = otvet_del.message;
                    this.color_s = "success";
                }
                this.init_forms();
                this.dialog_del_vib = false;
            });
        },
        new_zap(href) {
            if (href != this.$route.path) {
                this.$router.push(href);
            }
        },
        editItem(item) {
            let href = "/forms/edit_forms?id=" + item.id;
            if (href != this.$route.path) {
                this.$router.push(href);
            }
        },
        viewItem(id) {
            let href = "/forms/view_result_form?id=" + id;
            if (href != this.$route.path) {
                this.$router.push(href);
            }
        },
        saveExcel(id) {
            window.open(`${this.mainpath}/forms/get_excel/${id}`);
        },
        saveWord(id) {
            window.open(`${this.mainpath}/forms/get_word/${id}`);
        },
    },
}
</script>