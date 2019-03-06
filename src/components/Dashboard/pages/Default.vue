<template>
  <div class="w-100">
    <div>
      <h2 class="text-right">Logs Management Dashboard</h2>
    </div>
    <div class="d-flex py-1">
      <div class="flex-grow-1">
        <b-form-group class="mb-0" style="width: 400px">
          <b-input-group>
            <b-form-input
              v-model="filterInput"
              placeholder="Type to Search"
              class="form-control-sm"
              style="width: 300px"
            ></b-form-input>
            <b-btn class="ml-1 btn-sm" @click>Search</b-btn>
          </b-input-group>
        </b-form-group>
      </div>
    </div>

    <div>
      <b-table
        show-empty
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
            <b-row class="mb-2">
              <b-col sm="3" class="text-sm-right">
                <b>Age:</b>
              </b-col>
              <b-col>{{ row.item.age }}</b-col>
            </b-row>
            <b-row class="mb-2">
              <b-col sm="3" class="text-sm-right">
                <b>Is Active:</b>
              </b-col>
              <b-col>{{ row.item.isActive }}</b-col>
            </b-row>
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
        <span>{{totalRows}} traders found</span>
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
    this.debouncedSetFilter = _.debounce(this.setFilter, 500);
  },

  data() {
    return {
      sortBy: "id",
      sortDesc: true,
      filter: "",
      filterInput: "",
      perPage: 10,
      currentPage: 1,

      fields: [
        { key: "id", label: "ID", sortable: true, thStyle: "width: 80px" },
        { key: "email", label: "E-Mail", sortable: true },
        {
          key: "balance",
          label: "Balance",
          sortable: true,
          formatter: value => {
            return "$" + (value ? Number(value).toFixed(2) : "0.00");
          }
        },
        {
          key: "profit",
          label: "Profit",
          sortable: true,
          formatter: value => {
            return "$" + (value ? Number(value).toFixed(2) : "0.00");
          }
        },
        {
          key: "loss",
          label: "Loss",
          sortable: true,
          formatter: value => {
            return "$" + (value ? Number(value).toFixed(2) : "0.00");
          }
        },
        {
          key: "trades_count",
          label: "Trades Count",
          sortable: false,
          formatter: value => {
            return value ? parseInt(value) : 0;
          }
        },
        {
          key: "deposits_number",
          label: "Deposits",
          sortable: false,
          formatter: value => {
            return value ? parseInt(value) : 0;
          }
        },
        {
          key: "withdrawals_number",
          label: "Withdrawals",
          sortable: false,
          formatter: value => {
            return value ? parseInt(value) : 0;
          }
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
    setFilter() {
      this.filter = this.filterInput;
    },

    itemsProvider(ctx) {
      return this.$api({
        method: "GET",
        url: "/",
        params: {
          page: ctx.currentPage,
          rows: ctx.perPage,
          sort: ctx.sortBy,
          order: ctx.sortDesc ? "DESC" : "ASC",
          query: ctx.filter,
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