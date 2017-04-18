<template>
    <div class="search-widget well well-lg">
        <div class="row">
            <div class="col-xs-12 text-center">
                <slot name="header"></slot>
            </div>
        </div>
        <form @submit.prevent.stop="search">
            <div class="row">
                <div class="col-xs-6 text-center">
                    <span v-show="errors.has('city')" class="error-message">{{ errors.first('city') }}</span>
                </div>
                <div class="col-xs-6 text-center">
                    <span v-show="errors.has('state')" class="error-message">{{ errors.first('state') }}</span>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-6 text-right">
                    <div class="form-group">
                        <input :class="{'form-control': true, 'error': errors.has('city') }" v-validate type="text" name="city" data-vv-rules="required" v-model="city" placeholder="city" >
                    </div>
                </div>
                <div class="col-xs-6">
                    <div class="form-group">
                        <select :class="{'form-control': true, 'error': errors.has('state') }" v-model="state" name="state" v-validate.initial="state" data-vv-rules="min_value:1" >
                            <option v-for="(state, index) in stateList" :value="(index)">{{ state }}</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 text-center">
                    <button type="submit" class="btn btn-default">Search</button>
                </div>
            </div>
        </form>
    </div>
</template>

<style lang="scss">
    @import '../../sass/common.scss';
    .search-widget {
        padding: 2em 0;
    }

    .error {
        border: 1px solid #f00;
    }

    .error-message {
        color: #f00;
    }
</style>

<script>
    export default {
        props: [],
        mounted() {
            let searchTerms = [], query;
            let queries = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
            for(let i=0; i<queries.length; i++) {
                query = queries[i].split('=');
                if (query[0] != 'page') {
                    searchTerms[query[0]] = query[1];
                }
            }
            if ("city" in searchTerms && "state" in searchTerms) {
                this.city = searchTerms["city"];
                this.state = searchTerms["state"];
            } else {
                this.state = 0; //Preselect "State" from select
            }
        },
        methods: {
            search() {
                this.$validator.validateAll().then(() => {
                    if (!this.$validator.errorBag.errors.length) {
                        window.bookMe.Event.fire('city-state-search', {city: this.city, state: this.state});
                    }
                }).catch((e) => {
                    console.log(e);
                });
            }
        },
        data() {
            return {
                city: "",
                state: 0,
                type: null,
                stateList: window.states,
                email: ""
            }
        }
    }
</script>