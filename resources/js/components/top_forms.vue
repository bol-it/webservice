<template>
<div>
    <v-navigation-drawer v-model="drawer" color="blue-grey lighten-5" app>
        <v-divider class="ma-0"></v-divider>
        <v-list nav dense>
            <div v-show="false">{{current_sheet}}</div>
            <v-list-item-group v-model="selectedItem" :mandatory="mandatory" color="brown darken-4">
                <v-list-item v-for="(item, i) in items" :key="i" @click="perehod(item.href)">
                    <v-list-item-icon>
                        <v-icon v-text="item.icon"></v-icon>
                    </v-list-item-icon>
                    <v-list-item-content>
                        <v-list-item-title v-text="item.title"></v-list-item-title>
                    </v-list-item-content>
                </v-list-item>
            </v-list-item-group>
        </v-list>
    </v-navigation-drawer>
    <v-app-bar color="blue-grey lighten-5" height="48" outlined app elevation="1" class="pl-4">
        <v-tooltip right color="black">
            <template v-slot:activator="{ on, attrs }">
                <v-app-bar-nav-icon @click.stop="drawer = !drawer" v-on="on">
                    <v-icon>{{drawer ? 'mdi-menu' : 'mdi-menu-open'}}</v-icon>
                </v-app-bar-nav-icon>
            </template>
            <span>{{drawer ? 'Cкрыть меню' : 'Раскрыть меню'}}</span>
        </v-tooltip>
        <v-spacer></v-spacer>
        <v-toolbar-title class="text-uppercase font-weight-bold text-h5">Формы, отчеты</v-toolbar-title>
        <v-spacer></v-spacer>
    </v-app-bar>
</div>
</template>

<script>
export default {
    props: {
        mainpath: String,
    },
    data() {
        return {
            data: [],
            drawer: true,
            selectedItem: null,
            mandatory: false,
            duration: 10,
            offset: 0,
            easing: 'easeInOutCubic',
            container: '#scroll-target',
            items: [
                { title: 'Формы', href: '/forms', icon: 'mdi-book-information-variant' },
                { title: 'Отчеты', href: '/forms/reports', icon: 'mdi-book-edit' },
            ],
        }
    },
    computed: {
        current_sheet() {
            this.selectedItem = null;
            this.items.find((item, index) => {
                item.href === this.$route.path;
                if (item.href === this.$route.path) this.selectedItem = index;
            });
            if (this.selectedItem == null) {
                this.mandatory = false;
            } else {
                this.mandatory = true;
            }
            return this.selectedItem;
        },
        options() {
            return {
                duration: this.duration,
                offset: this.offset,
                easing: this.easing,
                container: this.container,
            }
        },
    },
    created() {

    },
    methods: {
        perehod(href) {
            if (href != this.$route.path) {
                this.$router.push(href);
            }
        },
    },
}
</script>
