export default class State {
    init() {
        window.bookMe.store = {
            searchResults: {
                data: null,
                total: null,
                currentPage: null,
                lastPage: null,
                perPage: null,
                nextPageUrl: null,
                prevPageUrl: null,
                from: null,
                to: null
            }
        }
    }

    updateSearchResults(results) {
        window.bookMe.store.searchResults = {
            data: results.data,
            total: results.total,
            currentPage: results.current_page,
            lastPage: results.last_page,
            perPage: parseInt(results.per_page),
            nextPageUrl: results.next_page_url,
            prevPageUrl: results.prev_page_url,
            from: results.from,
            to: results.to
        };
    }

    getSearchResults() {
        return window.bookMe.store.searchResults;
    }

    getStore() {
        return window.bookMe.store;
    }
}