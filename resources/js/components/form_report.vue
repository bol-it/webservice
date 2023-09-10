<template>
    <div v-resize="onResize">
        <component is="style">
            body{
                line-height: 1;
            }
            .scroll-container {
                display: block;
                max-height: {{ heightBlock }}px;
                overflow-y: auto;
                scroll-behavior: smooth;
            }
            .scroll-container1 {
                display: block;
                max-height: {{ heightBlock1 }}px;
                overflow-y: auto;
                scroll-behavior: smooth;
            }
        </component>
        <v-form ref="form" v-model="valid">
            <v-container class="fill-height px-7 pb-0 pt-3 scroll-container" fluid>
                <v-row class="mb-0">
                    <v-col cols="1" style="min-width: 100px; max-width: 100%;" class="flex-grow-1 flex-shrink-0">
                        <component v-bind:is="form_scheme.header.type" :id_comp="form_scheme.header.id"
                            :properties="form_scheme.header.properties" :styles="form_scheme.styles[form_scheme.header.type]"
                            :value_component="form_scheme.header.value">
                        </component>
                    </v-col>
                </v-row>
                <v-row v-for="(item, key) in form_scheme.items" :key="key"  class="my-0">
                    <v-col cols="1" style="min-width: 100px; max-width: 100%;" class="flex-grow-1 flex-shrink-0">
                        <component v-bind:is="item.type" :id_comp="item.id"
                            :styles="form_scheme.styles[item.type]"
                            :properties="item.properties" :value_component="item.value" v-model="item.value">
                        </component>
                    </v-col>
                </v-row>
                <v-row  class="my-0">
                    <v-col cols="1" style="min-width: 100px; max-width: 100%;" class="flex-grow-1 flex-shrink-0">
                        <component v-bind:is="form_scheme.submit.type" :id_comp="form_scheme.submit.id"
                            :properties="form_scheme.submit.properties" :styles="form_scheme.styles[form_scheme.submit.type]"
                            :submitButton="submitButton"
                            :disabled="!valid"
                            :value_component="form_scheme.submit.value">
                        </component>
                    </v-col>
                </v-row>
            </v-container>
        </v-form>
        <div>
            <v-snackbar v-model="snackbar" :color="color_s" :timeout="timeout">
                {{ text }}
                <v-btn color="white" text @click="snackbar = false">Закрыть</v-btn>
            </v-snackbar>
        </div>
    </div>
</template>

<style>

.form-line {
    cursor: move;
}
</style>

<script>
import form_text_field from './form_text_field.vue';
import form_textarea from './form_textarea.vue';
import form_text_number from './form_text_number.vue';
import form_select from './form_select.vue';
import form_radio_group from './form_radio_group.vue';
import form_checkbox from './form_checkbox.vue';
import form_rating from './form_rating.vue';
import form_date_field from './form_date_field.vue';
import form_time_field from './form_time_field.vue';
import form_header from './form_header.vue';
import form_button_submit from './form_button_submit.vue';
import form_text_float from './form_text_float.vue';

export default {
    components: {
        form_text_field,
        form_textarea,
        form_text_number,
        form_select,
        form_radio_group,
        form_checkbox,
        form_rating,
        form_date_field,
        form_time_field,
        form_header,
        form_button_submit,
        form_text_float,
    },
    props: {
        mainpath: String,
        value: Object,
        heightBlockDiv: {
			type: Number,
			default: null,
		},
        widthBlockDiv: {
			type: Number,
			default: null,
		},
        save_report: Function,
    },
    data: () => ({
        form_scheme: {},
        valid: false,
        snackbar: false,
        text: 'My timeout is set to 10000.',
        timeout: 10000,
        color_s: "success",
        itemHeader: {id: null, name: 'Название формы', type: 'form_header', properties: {label: 'Название формы', }, value: null, isactive: false,},
        itemSubmit: {id: null, name: 'Кнопка формы', type: 'form_button_submit', properties: {label: 'Сохранить', }, value: null, isactive: false,},
        items: [],
        show: false,
        active_index: null,
        active_field: false,
        active_header: false,
        active_submit: false,
        windowSize: { x: 0, y: 0, },
        heightBlock: 0,
        heightBlock1: 0,
        counter: 0,
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
    }),
    computed: {
        resultArray: {
            get: function () {
                this.$emit('input', this.form_scheme);
                return this.form_scheme;
            },
            set: function (val) {
                this.form_scheme = val;
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
            this.form_scheme = this.value;
            if (!this.form_scheme) {
                this.form_scheme = {};
                let itemHeader = JSON.parse(JSON.stringify(this.itemHeader));
                let items = JSON.parse(JSON.stringify(this.items));
                let itemSubmit = JSON.parse(JSON.stringify(this.itemSubmit));
                let styles = JSON.parse(JSON.stringify(this.styles));
                itemHeader.id = -1;
                itemSubmit.id = -2;
                this.form_scheme.header = itemHeader;
                this.form_scheme.items = items;
                this.form_scheme.submit = itemSubmit;
                this.form_scheme.styles = styles;
            }
            else {
                if (!this.form_scheme.hasOwnProperty('header')) {
                    let itemHeader = JSON.parse(JSON.stringify(this.itemHeader));
                    itemHeader.id = -1;
                    this.form_scheme.header = itemHeader;
                }
                if (!this.form_scheme.hasOwnProperty('items')) {
                    let items = JSON.parse(JSON.stringify(this.items));
                    this.form_scheme.items = items;
                }
                if (!this.form_scheme.hasOwnProperty('submit')) {
                    let itemSubmit = JSON.parse(JSON.stringify(this.itemSubmit));
                    itemSubmit.id = -2;
                    this.form_scheme.submit = itemSubmit;
                }
                if (!this.form_scheme.hasOwnProperty('styles')) {
                    let styles = JSON.parse(JSON.stringify(this.styles));
                    this.form_scheme.styles = styles;
                }
            }
            this.onResize();
        },
        onResize() {
            this.windowSize = { x: window.innerWidth, y: window.innerHeight };
            this.heightBlock = this.windowSize.y - 250;
            this.heightBlock1 = this.windowSize.y - 247;
            if (this.heightBlockDiv) {
                this.heightBlock = this.heightBlockDiv-3;
                this.heightBlock1 = this.heightBlockDiv;
            }
        },
        submitButton() {
            this.save_report();
        },
    },
}
</script>