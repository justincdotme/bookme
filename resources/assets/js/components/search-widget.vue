<template>
    <div class="search-widget well well-lg">
        <div class="row">
            <div class="col-xs-12 text-center">
                <slot name="header"></slot>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-6 text-right">
                <div class="form-group">
                    <input class="form-control" type="text" name="city" v-model="city" placeholder="city">
                </div>
            </div>
            <div class="col-xs-6">
                <div class="form-group">
                    <select class="form-control" name="state" v-model="state" >
                        <option v-for="(state, index) in stateList" :value="(index + 1)">{{ state }}</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12 text-center">
                <button type="button" class="btn btn-default" @click="search">Search</button>
            </div>
        </div>
    </div>
</template>

<style lang="scss">
    @import '../../sass/common.scss';
    .search-widget {
        padding: 2em 0;
    }
</style>

<script>
    export default {
        props: [],
        methods: {
            search() {
                window.bookMe.Event.fire('city-state-search', {city: this.city, state: this.state})
            }
        },
        data() {
            return {
                city: null,
                state: null,
                type: null,
                stateList: window.states
            }
        }
    }
</script>