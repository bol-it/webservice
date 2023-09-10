<template>
    <div class="fill-height" v-resize="onResize">
        <div v-if="error_load" class="fill-height pa-5">
            <v-row class="fill-height align-content-center justify-center">
                <v-col cols="1" style="min-width: 100px; max-width: 600px;" class="flex-grow-1 flex-shrink-0 pa-0 justify-space-between">
                    <v-card class="ma-5" outlined shaped elevation="0" width="600">
                        <v-alert text type="error">Ошибка загрузки</v-alert>
                        <v-card-actions class="mb-5">
                            <v-row justify="center" no-gutters>
                                <v-btn depressed color="error" @click="perehod('/forms/reports')" class="mx-2">
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
        <div v-if="final_save" class="fill-height pa-5">
            <v-row class="fill-height align-content-center justify-center">
                <v-col cols="1" style="min-width: 100px; max-width: 600px;" class="flex-grow-1 flex-shrink-0 pa-0 justify-space-between">
                    <v-card class="ma-5" outlined shaped elevation="0" width="600">
                        <v-alert text type="success">Ваш отчет успешно сохранен</v-alert>
                        <v-card-actions class="mb-5">
                            <v-row justify="center" no-gutters>
                                <v-btn depressed color="success" @click="perehod('/forms/reports')" class="mx-2">
                                    <v-icon>mdi-check</v-icon>
                                    ОК
                                </v-btn>
                            </v-row>
                        </v-card-actions>
                    </v-card>
                </v-col>
            </v-row>
        </div>
        <div v-if="((!final_save) && (!error_load) && (!load))" class="fill-height grey darken-3">
            <v-container class="fill-height pa-5" fluid>
                <v-card elevation="2" class="mx-auto" outlined :height="heightBlock" width="95%" max-height="100%">
                    <form-report :heightBlockDiv="heightBlock1" :widthBlockDiv="widthBlockDiv" :save_report="save_report" v-model="form_scheme"></form-report>
                </v-card>
            </v-container>
        </div>
        <div>
            <v-row justify="center">
                <v-dialog v-model="dialog_save" persistent max-width="400">
                    <v-card>
                        <v-card-title class="headline blue white--text" primary-title>
                            <v-icon dark large>mdi-information-outline</v-icon>
                            <v-spacer></v-spacer>
                            <strong>Внимание</strong>
                            <v-spacer></v-spacer>
                        </v-card-title>
                        <v-card-text class="pa-3 text-center"><strong>Вы собираетесь сохранить данные?</strong></v-card-text>
                        <v-card-actions>
                            <div class="flex-grow-1"></div>
                            <div class="text-xs-center">
                                <v-btn color="error" @click="dialog_save = false"><v-icon>mdi-cancel</v-icon>Отмена</v-btn>
                                <v-btn color="success" @click="save_form()"><v-icon>mdi-check</v-icon>Сохранить</v-btn>
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
import formReport from './form_report.vue';

export default
{
    components: {
        formReport,
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
        otvet_save: null,
        dialog_save: false,
        error_load: false,
        author_info: {},
        user_info: {},
        new_line: false,
    }),
    computed: {

    },
    watch: {

    },
    created () {
        let id = + this.id;
        if (id > 0) {
            this.init();
        }
        else {
            this.error_load = true;
            this.load = false;
        }
    },
    methods: {
        init() {
            axios.get(this.mainpath + '/forms/get_form?id=' + this.id)
            .then((response) => {
                this.otvet_load = response.data;
                this.form_scheme = this.otvet_load.body;
                this.error_load = false;
                this.load = false;
            })
            .catch(
                response => {
                    this.error_load = true;
                    this.load = false;
                    console.log('При загрузке данных произошла ошибка');
                }
            );
            this.onResize();
        },
        onResize() {
            this.windowSize = { x: window.innerWidth, y: window.innerHeight };
            this.heightBlock = this.windowSize.y - 150;
            this.widthBlockDiv = this.windowSize.x * 0.8;
            this.heightBlock1 = this.heightBlock;
        },
        save_form() {
            let formData = new FormData();
            let id_form = this.id;
            let form_scheme = JSON.stringify(this.form_scheme);
            let new_line = this.new_line;
            let is_podr = this.is_podr;

            formData.append('id_form', id_form);
            formData.append('form_scheme', form_scheme);

            let path_save = this.mainpath+'/forms/save_report';

            axios.post(
                path_save,
                formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                }
            )
            .then(response => {
                this.otvet_save = response.data;
                if (this.otvet_save.error == 1) {
                    this.snackbar = true;
                    this.text = response.statusText;
                    this.text += ". " + this.otvet_save.message;
                    this.color_s = "error";
                }
                if (this.otvet_save.success == 1) {
                    this.snackbar = true;
                    this.text = response.statusText;
                    this.text += ". " + this.otvet_save.message;
                    this.color_s = "success";
                    this.final_save = true;
                }
                this.dialog_save = false;
            })
            .catch(
                response => {
                    this.dialog_save = false;
                    console.log('При сохранении произошла неизвестная ошибка');
                }
            );
        },
        save_report() {
            this.dialog_save = true;
        },
        perehod (href) {
            if (href != this.$route.path) {
                this.$router.push(href);
            }
        },
    },
}
</script>