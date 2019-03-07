<template>
  <div class="w-100">
    <div>
      <h2 class="text-right">Logs Management Dashboard</h2>
    </div>

    <b-row>
      <b-col>
        <b-form-group label="Filter" label-for="filter">
          <b-form-input id="filter" v-model="filterInput" placeholder="Type to Search"></b-form-input>
        </b-form-group>
      </b-col>

      <b-col>
        <b-form-group label="Min Level" label-for="level">
          <b-form-select
            id="level"
            v-model="levels.selected"
            :options="levels.list"
            @input="reloadTable"
          />
        </b-form-group>
      </b-col>

      <b-col>
        <b-form-group label="Source" label-for="source">
          <b-form-select
            id="source"
            v-model="sources.selected"
            :options="sources.list"
            @input="reloadTable"
          />
        </b-form-group>
      </b-col>
    </b-row>

    <div>
      <b-table
        show-empty
        ref="table"
        class="w-100 table-sm"
        :sort-by.sync="sortBy"
        :sort-desc.sync="sortDesc"
        :items="itemsProvider"
        :current-page="currentPage"
        :per-page="perPage"
        :filter="filter"
        :fields="fields"
      >
        <template slot="show_details" slot-scope="row">
          <b-button
            size="sm"
            @click.stop="row.toggleDetails"
            class="mr-2"
          >{{ row.detailsShowing ? 'Hide' : 'Show'}} Details</b-button>
        </template>
        <template slot="row-details" slot-scope="row">
          <b-card>
            <tree-view :data="JSON.parse(row.item.context)" :options="{maxDepth: 3}"></tree-view>
            <tree-view :data="JSON.parse(row.item.extra)" :options="{maxDepth: 3}"></tree-view>
          </b-card>
        </template>
      </b-table>
    </div>

    <div class="d-flex">
      <div class="flex-grow-1">
        <b-pagination
          v-model="currentPage"
          :total-rows="totalRows"
          :per-page="perPage"
          class="my-0"
        ></b-pagination>
      </div>
      <div class="ml-2 align-self-center">
        <span>{{totalRows}} record(s) found</span>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";
import _ from "lodash";

let items = [];

export default {
  components: {},

  watch: {
    filterInput: function() {
      this.debouncedSetFilter();
    }
  },

  created() {
    let me = this;
    me.debouncedSetFilter = _.debounce(this.setFilter, 500);
    me.loadSources();
    Object.keys(log_levels).map(function(key, index) {
      me.levels.list.push({
        text: log_levels[key],
        value: key
      });
    });
  },

  data() {
    return {
      sources: {
        list: [],
        selected: ""
      },
      levels: {
        list: [],
        selected: 100
      },
      sortBy: "id",
      sortDesc: true,
      filter: "",
      filterInput: "",
      perPage: 50,
      currentPage: 1,

      fields: [
        {
          key: "created_at",
          label: "Timestamp",
          sortable: true,
          thStyle: "width: 180px"
        },
        {
          key: "level",
          label: "Level",
          sortable: false,
          thStyle: "width: 130px",
          formatter: value => {
            return log_levels[value];
          }
        },
        { key: "channel", label: "Channel", sortable: false },
        {
          key: "message",
          label: "Message",
          sortable: false
        },
        {
          key: "show_details",
          label: "Actions",
          sortable: false,
          thStyle: "width: 120px"
        }
      ],

      items: items,
      totalRows: items.length
    };
  },

  methods: {
    loadSources() {
      this.$api({
        method: "GET",
        url: "/",
        params: {
          action: "getSources"
        }
      }).then(response => {
        this.sources.list = response.data.sources;
        if (this.sources.list.length) {
          this.sources.selected = this.sources.list[0];
          this.reloadTable();
        } else {
          alert("No sources configured");
        }
      });
    },
    reloadTable() {
      this.$refs.table.refresh();
    },
    setFilter() {
      this.filter = this.filterInput;
    },

    itemsProvider(ctx) {
      if (!this.sources.selected) {
        return Promise.resolve([[]]);
      }
      return this.$api({
        method: "GET",
        url: "/",
        params: {
          page: ctx.currentPage,
          rows: ctx.perPage,
          sort: ctx.sortBy,
          order: ctx.sortDesc ? "DESC" : "ASC",
          query: ctx.filter,
          source: this.sources.selected,
          min_level: this.levels.selected,
          action: "getRecords"
        }
      })
        .then(response => {
          if (response.data.error) {
            alert(response.data.error);
          } else {
            const items = response.data.rows;
            this.totalRows = parseInt(response.data.total);
            return items || [];
          }
        })
        .catch(error => {
          return [];
        });
    }
  }
};
</script>

<style lang="scss">
</style>