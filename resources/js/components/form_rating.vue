<template>
    <div>
        <v-row class="pl-5 pt-2">
            <span class="font-weight-bold">{{properties_rating.label}}</span>
        </v-row>
        <v-row class="px-5">
            <v-rating
                v-model="value_rating"
                hover
                :dense="styles_rating.dense"
                :color="styles_rating.color"
                :background-color="styles_rating.color"
                :size="styles_rating.size"
                :length="properties_rating.length"
                :empty-icon="`${styles_rating.icon}-outline`"
                :full-icon="styles_rating.icon"
            >
            </v-rating>
        </v-row>
        <v-row class="pl-5">
            <span class="caption">{{ properties_rating.hint_availability ? properties_rating.hint : '' }}</span>
        </v-row>
    </div>
</template>

<script>

export default {
    components: {

    },
    props: {
        mainpath: String,
        value_component: Number,
        id_comp: Number,
        properties: Object,
        styles: Object,
    },
    data: () => ({
        value_rating: '',
        properties_rating: {},
        styles_rating: {},
        rules: {
          required: value => !!value || 'Поле обязательное для ввода',
          not_required: value => true,
        },
    }),
    computed: {

    },
    watch: {
        value_rating(val) {
            this.updateInput();
        },
    },
    created () {
        this.value_rating = this.value_component;
        this.properties_rating = this.properties;
        this.styles_rating = this.styles;
        //console.log(this.properties_rating);
    },
    methods: {
        updateInput() {
            if (!this.value_rating) {
                return this.$emit('input', '');
            }
            return this.$emit('input', this.value_rating);
        },
    },
}
</script>