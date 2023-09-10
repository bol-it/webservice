<template>
    <div>
        <v-text-field
            :label="properties_text.label"
            :outlined="styles_text.type_style == 'outlined'"
            :filled="styles_text.type_style == 'filled'"
            :dense="styles_text.dense"
            :clearable="styles_text.clearable"
            :rounded="styles_text.rounded"
            :rules="properties_text.required ? rules_date_required : rules_date_not_required"
            maxlength="10"
            v-model="value_text"
            append-icon="event"
            type="date"
            :min="min_date"
            :max="max_date"
            required
        ></v-text-field>
    </div>
</template>

<script>
import moment from 'moment';

export default {
    name: 'VInputDate',
    components: {

    },
    props: {
        mainpath: String,
        value: {
			type: String,
			default: '',
        },
        id_comp: Number,
        properties: Object,
        styles: {
			type: Object,
			default: () => (
                { dense: true, clearable: true, type_style: 'outlined', rounded: false, }
            ),
        },
        max_date_now: {
			type: Boolean,
			default: true,
		},
        min_date: {
			type: String,
			default: '1850-01-01',
		},
        max_date: {
			type: String,
			default: '9999-12-31',
        },
        label: {
            type: String,
			default: 'Надпись',
        },
        required: {
			type: Boolean,
			default: true,
		},
    },
    data: () => ({
        value_text: '',
        properties_text: {},
        styles_text: {},
        rules: {
          required: value => !!value || 'Поле обязательное для ввода',
          not_required: value => true,
        },
    }),
    computed: {
        rules_date_required() {
            return [
                (v) => {
                    if (!v || v.length < 1) return 'Поле дата не заполнено';
                    else
                    {
                        if (v.length > 10) return `Дата должна быть меньше ${moment('9999-12-31').format('DD.MM.YYYY')}`;
                        let min_date = this.min_date;
                        let max_date = this.max_date;
                        if (moment(v).isBefore(min_date)) return `Дата должна быть больше ${moment(min_date).format('DD.MM.YYYY')}`;
                        if (moment(v).isAfter(max_date)) return `Дата должна быть меньше ${moment(max_date).format('DD.MM.YYYY')}`;
                        if (this.properties_text.max_date_now) {
                            let now = moment();
                            if (moment(v).isAfter(now)) return `Дата должна быть меньше ${moment(now).add(1, 'days').format('DD.MM.YYYY')}`;
                        }
                        return true;
                    }
                }
            ]
        },
        rules_date_not_required() {
            return [
                (v) => {
                    if (!v || v.length < 1) return true;
                    else
                    {
                        if (v.length > 10) return `Дата должна быть меньше ${moment('9999-12-31').format('DD.MM.YYYY')}`;
                        let min_date = this.min_date;
                        let max_date = this.max_date;
                        if (moment(v).isBefore(min_date)) return `Дата должна быть больше ${moment(min_date).format('DD.MM.YYYY')}`;
                        if (moment(v).isAfter(max_date)) return `Дата должна быть меньше ${moment(max_date).format('DD.MM.YYYY')}`;
                        if (this.properties_text.max_date_now) {
                            let now = moment();
                            if (moment(v).isAfter(now)) return `Дата должна быть меньше ${moment(now).add(1, 'days').format('DD.MM.YYYY')}`;
                        }
                        return true;
                    }
                }
            ]
        },
    },
    watch: {
        value_text(val) {
            this.updateInput();
        },
    },
    created () {
        this.value_text = this.value;
        if (this.properties == undefined) {
            this.properties_text.max_date_now = this.max_date_now;
            this.properties_text.label = this.label;
            this.properties_text.required = this.required;
        }
        else {
            this.properties_text = this.properties;
            if (!this.properties_text.hasOwnProperty('max_date_now')) {
                this.properties_text.max_date_now = this.max_date_now;
            }
            if (!this.properties_text.hasOwnProperty('label')) {
                this.properties_text.label = this.label;
            }
            if (!this.properties_text.hasOwnProperty('required')) {
                this.properties_text.required = this.required;
            }
        }
        this.styles_text = this.styles;
        //console.log(this.properties_text);
    },
    methods: {
        updateInput() {
            if (!this.value_text) {
                return this.$emit('input', '');
            }
            return this.$emit('input', this.value_text);
        },
    },
}
</script>