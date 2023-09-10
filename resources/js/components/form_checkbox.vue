<template>
    <div>
        <v-row class="pl-5 pt-2">
        {{properties_check.label}}
        </v-row>
        <v-row>
            <v-col v-for="(item, key) in properties_check.items" :key="key" cols="12" :sm="styles_check.position == 'column' ? 12: 4" :md="styles_check.position == 'column' ? 12: 4" class="py-0">
                <v-checkbox :value="item" :label="item" :dense="styles_check.dense" :color="styles_check.color" v-model="value_check">
                </v-checkbox>
            </v-col>
        </v-row>
    </div>
</template>

<script>

export default {
    components: {

    },
    props: {
        mainpath: String,
        value_component: Array,
        id_comp: Number,
        properties: Object,
        styles: Object,
    },
    data: () => ({
        value_check: '',
        properties_check: {},
        styles_check: {},
        rules_radio: [],
        rules: {
          required: value => !!value || 'Поле обязательное для ввода',
          not_required: value => true,
        },
    }),
    computed: {

    },
    watch: {
        value_check(val) {
            this.updateInput();
        },
    },
    created () {
        this.value_check = this.value_component;
        this.properties_check = this.properties;
        if (this.properties_check.required) {
            this.rules_radio = this.rules_required;
        }
        else {
            this.rules_radio = true;
        }
        this.styles_check = this.styles;
        //console.log(this.properties_text);
    },
    methods: {
        updateInput() {
            if (!this.value_check) {
                return this.$emit('input', '');
            }
            return this.$emit('input', this.value_check);
        },
    },
}
</script>