<template>
    <div>
        <v-row class="px-5 pt-2 pb-1">
            <div class="w3-small">Мин.значение - {{ properties_text.min }}, макc.значение - {{properties_text.max}}, шаг - {{properties_text.step}}</div>
        </v-row>
        <v-row class="px-5">
            <v-btn outlined color="grey lighten-4" tile class="grey darken-3" :style="styles_text.dense ? 'height:40px;' : 'height:56px;'"
                @mousedown="decrease()" @click="stop_interval" @mouseleave="stop_interval" @mouseup="stop_interval"
            >
                <v-icon>mdi-minus</v-icon>
            </v-btn>
            <v-text-field
                :label="properties_text.label"
                :placeholder="properties_text.placeholder_availability ? properties_text.placeholder : ''"
                outlined
                :hint="properties_text.hint_availability ? properties_text.hint : ''"
                :dense="styles_text.dense"
                :clearable="properties_text.readonly ? false: styles_text.clearable"
                :rules="rules_text"
                :readonly="properties_text.readonly"
                class="rounded-0"
                v-mask="mask"
                v-model="value_text"
            >
            </v-text-field>
            <v-btn outlined color="grey lighten-4" tile class="grey darken-3" :style="styles_text.dense ? 'height:40px;' : 'height:56px;'"
                @mousedown="increase()" @click="stop_interval" @mouseleave="stop_interval" @mouseup="stop_interval"
            >
                <v-icon>mdi-plus</v-icon>
            </v-btn>
        </v-row>
    </div>
</template>

<style>

</style>

<script>
import { mask } from 'vue-the-mask';

export default {
    name: 'VInputNumber',
    directives: {
        mask,
    },
    components: {

    },
    props: {
        mainpath: String,
        value_component: {
            type: String,
            default: '0',
        },
        id_comp: Number,
        properties: {
            type: Object,
            default: () => (
                { placeholder_availability: false, placeholder: '', hint_availability: false, hint: '', min: 0,}
            ),
        },
        styles: {
            type: Object,
            default: () => (
                { dense: true, clearable: false,}
            ),
        },
        label: {
			type: String,
			default: 'Надпись',
		},
        min: {
			type: [String, Number],
			default: 1,
		},
		max: {
			type: [String, Number],
			default: 9999,
		},
		maxLength: {
			type: Number,
			default: 4,
		},
        step: {
			type: [String, Number],
			default: 1,
		},
        readonly: {
			type: Boolean,
			default: false,
		},
        dense: {
			type: Boolean,
			default: false,
		},
        value: [String, Number],
    },
    data () {
        return {
            isDown: false,
            interval: 0,
            value_text: 0,
            min_number: 0,
            max_number: 9999,
            step_number: 1,
            maxLength_number: 4,
            properties_text: {},
            styles_text: {},
            rules_required: [
                (v) => {
                    if (!v || v.length < 1) return 'Поле обязательное для ввода';
                    if (parseInt(v) < this.properties_text.min) return 'Число должно быть большe ' + (parseInt(this.properties_text.min) - 1);
                    if (parseInt(v) > this.properties_text.max) return 'Число должно быть меньше ' + (parseInt(this.properties_text.max) + 1);
                    return true;
                }
            ],
        }
    },
    computed: {
		mask() {
			let mask = '';
            this.maxLength_number = this.properties_text.max.toString().length;
			for (let i = 0; i < this.maxLength_number; i++) mask = `${mask}#`;
			return mask;
		},
    },
    watch: {
        value_text(val) {
            this.updateInput();
        },
    },
    created () {
        this.value_text = this.value_component.toString();
        if (this.value !== undefined) {
            this.value_text = this.value.toString();
        }
        this.properties_text = this.properties;
        //console.log(this.properties_text);
        if (this.properties_text.hasOwnProperty('required')) {
            if (this.properties_text.required) {
                this.rules_text = this.rules_required;
            }
            else {
                this.rules_text = true;
            }
        }
        else {
            this.rules_text = this.rules_required;
        }
        if (!this.properties_text.hasOwnProperty('label')) {
            this.properties_text.label = this.label;
        }
        if (!this.properties_text.hasOwnProperty('readonly')) {
            this.properties_text.readonly = this.readonly;
        }
        if (this.properties_text.hasOwnProperty('min')) {
            if (this.properties_text.min != undefined) {
                this.min_number = parseInt(this.properties_text.min);
                this.properties_text.min = parseInt(this.properties_text.min);

            }
            else {
                this.min_number = parseInt(this.min);
                this.properties_text.min = parseInt(this.min);
            }
        }
        else {
            this.min_number = parseInt(this.min);
            this.properties_text.min = parseInt(this.min);
        }
        if (this.properties_text.hasOwnProperty('max')) {
            if (this.properties_text.max != undefined) {
                this.max_number = parseInt(this.properties_text.max);
                this.properties_text.max = parseInt(this.properties_text.max);
            }
            else {
                this.max_number = parseInt(this.max);
                this.properties_text.max = parseInt(this.max);
            }
        }
        else {
            this.max_number = parseInt(this.max);
            this.properties_text.max = parseInt(this.max);
        }
        if (this.properties_text.hasOwnProperty('step')) {
            if (this.properties_text.step != undefined) {
                this.step_number = parseInt(this.properties_text.step);
                this.properties_text.step = parseInt(this.properties_text.step);
            }
            else {
                this.step_number = parseInt(this.step);
                this.properties_text.step = parseInt(this.step);
            }
        }
        else {
            this.step_number = parseInt(this.step);
            this.properties_text.step = parseInt(this.step);
        }
        if (this.properties_text.step > this.properties_text.max) {
            this.properties_text.step = this.properties_text.max;
        }
        this.styles_text = this.styles;
        if (!this.styles_text.hasOwnProperty('dense')) {
            this.styles_text.dense = this.dense;
        }
        //console.log(this.properties_text);
    },
    methods: {
        stop_interval()
        {
            this.isDown = false;
            clearInterval(this.interval);
        },
		increase() {
            this.isDown = true;
            if (this.isDown) {
                if (isNaN(parseInt(this.value_text))) {
                    this.value_text = this.properties_text.step.toString();
                }
                this.interval = setInterval(() => {
                    if (parseInt(this.value_text) + parseInt(this.properties_text.step) <= parseInt(this.properties_text.max)) {
                        this.value_text = (parseInt(this.value_text) + parseInt(this.properties_text.step)).toString();
                    }
                    else {
                        if (parseInt(this.value_text) < parseInt(this.properties_text.max)) {
                            //console.log(parseInt(this.value_text));
                            //console.log(this.properties_text.max);
                            this.value_text = this.properties_text.max.toString();
                        }
                    }
                }, 60);
			}
            else {
                clearInterval(this.interval);
            }
		},
		decrease () {
            this.isDown = true;
            if (this.isDown) {
                if (isNaN(parseInt(this.value_text))) {
                    this.value_text = this.properties_text.min.toString();
                }
                this.interval = setInterval(() => {
                    if (parseInt(this.value_text) - parseInt(this.properties_text.step) >= parseInt(this.properties_text.min)) {
                        this.value_text = (parseInt(this.value_text) - parseInt(this.properties_text.step)).toString();
                    }
                    else {
                        if (parseInt(this.value_text) > parseInt(this.properties_text.min)) {
                            this.value_text = this.properties_text.min.toString();
                        }
                    }
                }, 60);
            }
            else {
                clearInterval(this.interval);
            }
		},
        updateInput() {
            if (!this.value_text) {
                return this.$emit('input', '');
            }
            return this.$emit('input', this.value_text);
        },
    },
}
</script>