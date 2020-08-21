<template>
    <div class="row">
        <div class="row ml-5">
            <div class="form-row">
                <div class="form-group col-md-12">
                    <label for="filterStatus">Estado</label>
                    <select id ="filterStatus" class="form-control" v-model="serverParams.filterStatus" v-on:input="changeFilter"   >

                        <option value="-1">(Todos)</option>
                        <option value="1">Activo</option>
                        <option value="0">Inactivo</option>
                    </select>
                </div>
            </div>
        </div>
        <b-modal id="frmUser"
                 title="Editar usuario"
                 @show="resetModal"
                 @hidden="resetModal"
                 @ok="handleOk"
        >
            <div class="form-row">
                <div class="form-group col-md-12">
                    <label for="name">Nombre</label>
                    <input v-model="model.name" id="name" class="form-control"   required >
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-12">
                    <label for="status">Estado</label>
                    <select id ="status" class="form-control" v-model="model.status"   >
                        <option value="" selected disabled hidden>Seleccione uno </option>
                        <option value="1">Activo</option>
                        <option value="0">Inactivo</option>
                    </select>
                </div>
            </div>
        </b-modal>
        <div class="col-md-12">
            <vue-good-table
                mode="remote"
                @on-page-change="onPageChange"
                @on-sort-change="onSortChange"
                @on-column-filter="onColumnFilter"
                @on-per-page-change="onPerPageChange"
                @on-search="onSearch"
                :totalRows="totalRecords"
                :isLoading.sync="isLoading"
                :pagination-options="{
                    enabled: true,
                    }"
                :rows="rows"
                :columns="columns"
                :globalSearch="true"

                styleClass="vgt-table striped"
                :search-options="{
                    enabled: true,
                    //trigger: 'enter',
                    skipDiacritics: true,
                    placeholder: 'Ingrese nombre para buscar',
                  }"

            >
                <template slot="table-row" slot-scope="props">
            <span v-if="props.column.field == 'acciones'">
                <button  class="btn btn-primary" v-on:click="loadItem(props.row.id)" >Editar</button>
                <!--<span style="font-weight: bold; color: blue;">{{props.row.age}}</span>-->
            </span>
                    <span v-else-if="props.column.field == 'status'">
                <span v-if="props.row.status == '1'">
                    <span class="badge badge-success">Activo</span>
                </span>
                <span  v-else>
                    <span class="badge badge-secondary">Inactivo</span>
                </span>
            </span>
                    <span v-else>
              {{props.formattedRow[props.column.field]}}
            </span>
                </template>
            </vue-good-table>
        </div>

    </div>
</template>

<script>
    export default {
        name: 'edit-users',
        data(){
            return {
                filterStatus:"1",
                model:{
                    name:'',
                    status:0
                },
                isLoading: false,
                columns: [
                    {
                        label: 'id',
                        field: 'id',
                    },
                    {
                        label: 'Nombre',
                        field: 'name',
                    },
                    {
                        label: 'Correo',
                        field: 'email',
                    },
                    {
                        label: 'Estado',
                        field: 'status',
                    },
                    {
                        label: 'Acciones',
                        field: 'acciones',
                    }


                ],
                rows: [],
                totalRecords: 0,
                serverParams: {
                    columnFilters: {
                    },
                    sort: {
                        field: '',
                        type: '',
                    },
                    page: 1,
                    perPage: 10,
                    filterStatus:"-1"
                }

            };
        },
        methods: {
            changeFilter()
            {
                this.loadItems();
            },
            resetModal() {
                //this.model={};
                //this.isLoading=false;
            },
            handleOk(bvModalEvt) {
                // Prevent modal from closing
                bvModalEvt.preventDefault();
                // Trigger submit handler
                this.saveItem();
            },
            loadItem(id)
            {
                this.isLoading=true;
                this.axios.get( 'users/'+id)
                    .then( (response) =>{
                        this.model=response.data;
                        this.$bvModal.show('frmUser')
                    })
                    .catch((error) =>{
                        console.log(error)
                    })
                    .finally(()=>
                    {
                        this.isLoading=false;
                    });

            },
            saveItem()
            {
                this.isLoading=true;
                this.axios.post( 'users',this.model)
                    .then( (response) =>{
                        if(response.data.success)
                        {
                            this.$bvModal.hide('frmUser')
                            this.loadItems();
                        }

                    })
                    .catch((error) =>{
                        console.log(error)
                    })
                    .finally(()=>
                    {
                        this.isLoading=false;
                    });

            },
            onSearch :function(params)
            {
                this.updateParams({page: params.page});
                this.updateParams({searchTerm: params.searchTerm});
                this.loadItems();
            },
            say: function (message) {
                alert(message)
            },
            updateParams(newProps) {
                this.serverParams = Object.assign({}, this.serverParams, newProps);
            },

            onPageChange(params) {
                this.updateParams({page: params.currentPage});
                this.loadItems();
            },

            onPerPageChange(params) {
                this.updateParams({perPage: params.currentPerPage});
                this.loadItems();
            },

            onSortChange(params) {
                this.updateParams({
                    sort: [{
                        type: params.sortType,
                        field: this.columns[params.columnIndex].field,
                    }],
                });
                this.loadItems();
            },

            onColumnFilter(params) {
                this.updateParams(params);
                this.loadItems();
            },
            // load items is what brings back the rows from server
            loadItems() {

                /*getFromServer(this.serverParams).then(response => {
                    this.totalRecords = response.totalRecords;
                    this.rows = response.rows;
                });*/
                this.isLoading=true;
                this.axios.get( 'users/table',{'params':this.serverParams})
                    .then( (response) =>{
                        //console.log(response.data.data);
                        this.totalRecords = response.data.total_records;
                        this.rows = response.data.data;
                        this.isLoading=false;
                    })
                    .catch((error) =>{

                        console.log(error)
                    })
                    .finally(()=>
                    {
                        this.isLoading=false;
                    });

            }

        },
        mounted() {
            this.loadItems();
        }
    };
</script>
