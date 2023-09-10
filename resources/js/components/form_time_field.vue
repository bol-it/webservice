<template>
    <div>
        <v-text-field
            :label="properties_text.label"
            :hint="properties_text.hint_availability ? properties_text.hint : ''"
            :outlined="styles_text.type_style == 'outlined'"
            :filled="styles_text.type_style == 'filled'"
            :dense="styles_text.dense"
            :clearable="styles_text.clearable"
            :rounded="styles_text.rounded"
            :rules="properties_text.required ? rules_date_required : rules_date_not_required"
            maxlength="10"
            v-model="value_text"
            append-icon="mdi-clock"
            type="time"
            :min="min_time"
            :max="max_time"
            required
        ></v-text-field>
    </div>
</template>

<script>
import moment from 'moment';

export default {
    components: {

    },
    props: {
        mainpath: String,
        value_component: String,
        id_comp: Number,
        properties: Object,
        styles: Object,
    },
    data: () => ({
        value_text: '',
        properties_text: {},
        styles_text: {},
        min_time: '06:00',
        max_time: '23:00',
        max_time_now: true,
        rules: {
          required: value => !!value || 'Поле обязательное для ввода',
          not_required: value => true,
        },
    }),
    computed: {
        rules_date_required() {
            return [
                (v) => {
                    if (!v || v.length < 1) return 'Поле время не заполнено';
                    else
                    {
                        let min_time = moment(this.min_time, "HH:mm:ss");
                        let max_time = moment(this.max_time, "HH:mm:ss");
                        let m = moment(v, "hh:mm:ss");
                        if (this.properties_text.max_time_now) {
                            if (m.isBefore(min_time)) return `Время должно быть больше ${min_time.subtract(1, 'm').format('HH:mm')}`;
                            if (m.isAfter(max_time)) return `Время должно быть меньше ${max_time.add(1, 'm').format('HH:mm')}`;
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
                        let min_time = moment(this.min_time, "HH:mm:ss");
                        let max_time = moment(this.max_time, "HH:mm:ss");
                        let m = moment(v, "HH:mm:ss");
                        if (this.properties_text.max_time_now) {
                            if (m.isBefore(min_time)) return `Время должно быть больше ${min_time.subtract(1, 'm').format('HH:mm')}`;
                            if (m.isAfter(max_time)) return `Время должно быть меньше ${max_time.add(1, 'm').format('HH:mm')}`;
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
        this.value_text = this.value_component;
        this.properties_text = this.properties;
        this.styles_text = this.styles;
        if (!this.properties_text.hasOwnProperty('max_time_now')) {
            this.properties_text.max_time_now = this.max_time_now;
        }
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