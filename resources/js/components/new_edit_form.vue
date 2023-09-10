<template>
    <div class="fill-height" v-resize="onResize">
        <div v-if="load" class="fill-height pa-5">
            <v-row class="fill-height align-content-center justify-center">
                <v-col class="subtitle-1 text-center" cols="12">Идет загрузка данных. Подождите...</v-col>
                <v-col cols="1" style="min-width: 100px; max-width: 600px;" class="flex-grow-1 flex-shrink-0 pa-0 justify-space-between">
                    <v-progress-linear color="blue-grey darken-5" indeterminate rounded height="6"></v-progress-linear>
                </v-col>
            </v-row>
        </div>
        <div v-if="((final_save) && (new_forms))" class="fill-height pa-5">
            <v-row class="fill-height align-content-center justify-center">
                <v-col cols="1" style="min-width: 100px; max-width: 600px;" class="flex-grow-1 flex-shrink-0 pa-0 justify-space-between">
                    <v-card class="ma-5" outlined shaped elevation="0" width="600">
                        <v-alert text type="success">Ваша форма под номером {{number_forms}} успешно создана</v-alert>
                        <v-card-actions class="mb-5">
                            <v-row justify="center" no-gutters>
                                <v-btn depressed color="success" @click="perehod('/forms')" class="mx-2">
                                    <v-icon>mdi-check</v-icon>
                                    ОК
                                </v-btn>
                            </v-row>
                        </v-card-actions>
                    </v-card>
                </v-col>
            </v-row>
        </div>
        <div v-if="((final_save) && (!new_forms))" class="fill-height pa-5">
            <v-row class="fill-height align-content-center justify-center">
                <v-col cols="1" style="min-width: 100px; max-width: 600px;" class="flex-grow-1 flex-shrink-0 pa-0 justify-space-between">
                    <v-card class="ma-5" outlined shaped elevation="0" width="600">
                        <v-alert text type="success">Ваша форма под номером {{number_forms}} успешно изменена</v-alert>
                        <v-card-actions class="mb-5">
                            <v-row justify="center" no-gutters>
                                <v-btn depressed color="success" @click="perehod('/forms')" class="mx-2">
                                    <v-icon>mdi-check</v-icon>
                                    ОК
                                </v-btn>
                            </v-row>
                        </v-card-actions>
                    </v-card>
                </v-col>
            </v-row>
        </div>
        <v-stepper v-model="step" v-if="((!final_save) && (!error_load) && (!load))">
            <v-stepper-header style="height:50px;">
                <v-stepper-step :complete="step > 1" step="1" style="height:50px;">Создание формы</v-stepper-step>
            </v-stepper-header>
             <v-stepper-items>
                <v-stepper-content step="1" class="pa-1">
                    <v-row class="mb-1 ml-0 justify-center" v-if="!load">
                        <form-creater
                            :mainpath="mainpath" :heightBlockDiv="heightBlock" :widthBlockDiv="widthBlockDiv" :items_form="items_form" v-model="form_scheme"
                            :itemHeader="itemHeader" :itemSubmit="itemSubmit" :styles="styles"
                        ></form-creater>
                    </v-row>
                    <v-row class="mb-1 ml-1 mr-1 justify-space-between">
                        <v-btn color="secondary" @click="perehod('/forms')"><v-icon left>mdi-door-open</v-icon> Выйти</v-btn>
                        <div v-if="!create_form" class="w3-panel w3-pale-red w3-leftbar w3-border-red ma-0 align-self-center">! {{errorStep1}}</div>
                        <v-btn color="success" @click="dialog_save = true" :disabled="!create_form">Сохранить <v-icon right>mdi-content-save</v-icon></v-btn>
                    </v-row>
                </v-stepper-content>
             </v-stepper-items>
        </v-stepper>
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
import formCreater from './form_creater.vue';

export default
{
    components: {
        formCreater,
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
        structure: [],
        step: 1,
        items_form: [],
        itemHeader: {id: null, name: 'Название формы', type: 'form_header', properties: {label: 'Название формы', }, value: null, isactive: false,},
        itemSubmit: {id: null, name: 'Кнопка формы', type: 'form_button_submit', properties: {label: 'Сохранить', }, value: null, isactive: false,},
        styles: {
            form_text_field: {
                dense: true, clearable: true, type_style: 'outlined', rounded: false,
            },
            form_textarea: {
                dense: true, clearable: true, type_style: 'outlined', rounded: false,
            },
            form_text_number: {
                dense: true, clearable: true,
            },
            form_select: {
                dense: true, clearable: true, type_style: 'outlined', rounded: false,
            },
            form_radio_group: {
                dense: false, position: 'column', color: '#3F51B5',
            },
            form_checkbox: {
                dense: false, position: 'column', color: '#3F51B5',
            },
            form_rating: {
                dense: false, icon: 'mdi-heart', color: '#3F51B5', size: 25,
            },
            form_date_field: {
                dense: true, clearable: true, type_style: 'outlined', rounded: false,
            },
            form_time_field: {
                dense: true, clearable: true, type_style: 'outlined', rounded: false,
            },
            form_text_snils_field: {
                dense: true, clearable: true, type_style: 'outlined', rounded: false,
            },
            form_header: {
                color: '#3F51B5', bold: true,
            },
            form_button_submit: {
                color: '#167719', rounded: false, dark: true, outlined: false,
            },
            form_text_float: {
                dense: true, clearable: true, type_style: 'outlined', rounded: false,
            },
        },
        form_scheme: null,
        windowSize: { x: 0, y: 0, },
        heightBlock: 0,
        heightBlock1: 0,
        widthBlockDiv: 0,
        valid_step3: false,
        date_answer: '',
        can_change_form: [],
        dialog_can_change_form: false,
        can_view: [],
        dialog_can_view: false,
        is_anybody_view_data: false,
        dialog_save: false,
        error_load: false,
        errorStep1: '',
        errorStep2: '',
        errorStep3: '',
        snackbar: false,
        text: 'My timeout is set to 10000.',
        timeout: 10000,
        color_s: "success",
        final_save: false,
        number_forms: 0,
        new_forms: false,
        load: true,
        otvet_load: null,
        otvet_save: null,
    }),
    computed: {
        create_form() {
            if (this.form_scheme) {
                let items = JSON.parse(JSON.stringify(this.form_scheme.items));
                if (items.length > 0) {
                    for (let index = 0; index < items.length; index++) {
                        const element = items[index];
                        let double = items.filter((item) => item.properties.label == element.properties.label);
                        if (double.length > 1) {
                            this.errorStep1 = `Дубликат надписи - "${double[0].properties.label}"`;
                            return false;
                        }
                    }
                    return true;
                }
                else {
                    this.errorStep1 = 'Форма не создана';
                    return false;
                }
            }
            else {
                this.errorStep1 = 'Форма не создана';
                return false;
            }
        },
    },
    watch: {

    },
    created () {
        this.init();
    },
    methods: {
        init() {
            let id = + this.id;
            if (id > 0) {
                axios.get(this.mainpath + '/forms/get_form?id=' + this.id)
                .then((response) => {
                    this.otvet_load = response.data;
                    this.itemHeader = this.otvet_load.body.header;
                    this.items_form = this.otvet_load.body.items;
                    this.itemSubmit = this.otvet_load.body.submit;
                    this.styles = this.otvet_load.body.styles;
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
            }
            else {
                this.error_load = false;
                this.load = false;
            }
            this.onResize();
        },
        onResize() {
            this.windowSize = { x: window.innerWidth, y: window.innerHeight };
            this.heightBlock = this.windowSize.y - 255;
            this.widthBlockDiv = this.windowSize.x * 0.55;
            this.heightBlock1 = this.windowSize.y - 170;
        },
        delete_can_change_form(item) {
            let index = this.can_change_form.findIndex(el => el.id === item.id);
            this.can_change_form.splice(index, 1);
        },
        delete_can_view(item) {
            let index = this.can_view.findIndex(el => el.id === item.id);
            this.can_view.splice(index, 1);
        },
        anybody_view_data() {
            if (this.is_anybody_view_data) {
                this.can_view = [];
            }
        },
        save_form () {
            let formData = new FormData();
            let id = this.id;
            let form_scheme = JSON.stringify(this.form_scheme);

            formData.append('id', id);
            formData.append('form_scheme', form_scheme);

            let path_save = this.mainpath+'/forms/save_form';

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
                    this.number_forms = this.otvet_save.id_forms;
                    this.new_forms = this.otvet_save.new;
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
        perehod (href) {
            if (href != this.$route.path) {
                this.$router.push(href);
            }
        },
    },
}
</script>