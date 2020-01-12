<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card mt-5">
                    <div class="card-header"> <i class="fas fa-stream"></i> Hierarchy trees</div>

                    <div class="card-body">

                        <!------------------ Select Main class ------------------>

                        <label for="exampleFormControlSelect1">Select which class set you want to see:</label>
                        <div class="input-group">
                            <select class="form-control" id="exampleFormControlSelect1" v-model="selectedClass" v-on:change="fetchSubClasses(selectedClass)">
                                <option v-for="classh in classSelection" v-bind:key="classh.cid" :value="classh.cid" >{{ classh.name }}</option>
                            </select>
                            <div class="input-group-append">
                                <button type="button" class="btn btn-success align-bottom" data-toggle="modal" data-target="#add_modal">
                                    <i class="fas fa-plus"></i> Add Class
                                </button>
                            </div>
                        </div>

                        <!--------------------- Search input ----------------------->
                        <div class="mt-3 mb-5">
                            <vue-bootstrap-typeahead placeholder="Search for the class by name"  v-model="superclassName" :data="autoComplete" class="w-100"/>
                            <button class="btn btn-primary btn-sm btn-block mt-1" v-on:click="findClass(superclassName)" type="button"> <i class="fas fa-search"></i> Find</button>
                        </div>

                        <!---------------- Interactive Tree View ------------------->

                         <div id="alert_of_empty" style="display:none;"> 
                            <div class="alert alert-warning" role="alert">
                                This class is empty <a data-target="#add_modal" data-toggle="modal" href="#add_modal" class="alert-link">click here</a> if you want to make addition. <br>
                                Or if you want to get rid of if <a href="#" v-on:click="deleteNode(classCid)" class="alert-link">click here</a>
                            </div>
                         </div>

                        <div id="treee" v-if="subClasses != null  && subClasses.length != 0"> 
                            <b-tree-view :data="subClasses" :renameNodeOnDblClick="false" :contextMenuItems="items" @contextMenuItemSelect="menuItemSelected" ref="tree"></b-tree-view>
                        </div>  
                        

                        <!------------------------------------- Update Modal --------------------------------------------->
                        <form @submit.prevent="updateNode">
                            <div class="modal fade" id="update_modal" tabindex="-1" role="dialog" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLongTitle">{{ nodee.name }}</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">

                                    <p>Selected Node ID: {{ nodee.cid }}<br>Selected Node: {{ nodee.name }}<br>Node Parent ID: {{ nodee.pid }}</p>

                                    
                                        <div class="form-group">
                                            <label for="nameInput_update">Name</label>
                                            <input name="node_name" type="name" class="form-control" id="nameInput_update" placeholder="Enter Node name" v-model="nodee.name" required>
                                        </div>

                                        <div class="form-group">
                                            <label for="node_parent_update">Select Parent</label>
                                            <select name="node_parent" class="form-control" id="node_parent_update" v-model="nodee.pid" required>
                                            <option v-for="classh in allClasses" v-bind:key="classh.cid" :value="classh.cid" >{{ classh.name }}</option>
                                            </select>
                                        </div>


                                    
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary">Save</button>
                                </div>
                                </div>
                            </div>
                            </div>
                        </form>

                        <!--------------------------------------------- Add Modal ------------------------------------------------>
                        <form @submit.prevent="addNode">
                            <div class="modal fade" id="add_modal" tabindex="-1" role="dialog" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLongTitle">Add new Node</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    
                                        <div class="form-group">
                                            <label for="nameInput_add">Name</label>
                                            <input name="node_name" type="name" class="form-control" id="nameInput_add" placeholder="Enter Node name" v-model="nodee.name" required>
                                        </div>

                                        <div class="form-group">
                                            <label for="node_parent_add">Select Parent</label>
                                            <select name="node_parent" class="form-control" id="node_parent_add" v-model="nodee.pid" required>
                                            <option :value="0" >**Super Parent**</option>
                                            <option v-for="classh in subClassesList" v-bind:key="classh.cid" :value="classh.cid" >{{ classh.name }}</option>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label for="abstract_add">Abstract class</label>
                                            <select name="abstract" class="form-control" id="abstract_add" v-model="nodee.abstract" required>
                                            <option :value="1">Yes</option>
                                            <option :value="0">No</option>
                                            </select>
                                        </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-success"><i class="fas fa-plus"></i> Add</button>
                                </div>
                                </div>
                            </div>
                            </div>
                        </form>

                    </div>
                </div>

                <!---------------------------------Parent Table show ------------------------------------->
                <div v-if="itemss.length != 0">
                    <h3 class="mt-3"> Parent classes rows </h3>
                    <hr>

                    <div class="overflow-auto mt-2">
                        <b-table
                        id="my-table"
                        :items="itemss"
                        :per-page="perPage"
                        :current-page="currentPage"
                        small
                        ></b-table>

                        <p class="mt-3 text-center">Current Page: {{ currentPage }}</p>

                        <b-pagination
                        v-model="currentPage"
                        :total-rows="rows"
                        :per-page="perPage"
                        aria-controls="my-table"
                        align="center"
                        ></b-pagination>
                    </div>
                </div>



            </div>
        </div>
    </div>
</template>

<script>

    export default {
        mounted() {
            this.fetchAllClasses();
            this.fetchSubClasses(this.selectedClass);
        },

        computed: {
            rows() {
                try {
                    return this.itemss.length
                } catch (error) {
                    return 0
                }
                 
            }
        },

        data() {
            return {
                //Class cid which loaded right now
                classCid:null,

                //Subclasses that are printed by BOOTSTRAP tree
                subClasses: [],
                subClassesList:[],

                //All fetched classes
                allClasses:[],
                autoComplete:[],

                //SuperClasses of class
                superClasses:[],
                superclassName: '',

                //Classes superParents that are in select input
                classSelection:[],

                //Selected class which showing
                selectedClass: 1,

                //Context menu buttons
                items: [{ code: 'ADD_CHILD_NODE', label: 'Add child' }, 
                        { code: 'UPDATE_NODE', label: 'Update node' },
                        { code: 'DELETE_NODEE', label: 'Delete node' },
                        { code: 'SHOW_PARENTS', label: 'Show Parents' },
                        ],
                response: [],
                //selected node by update or delete
                nodee:{
                    cid:"",
                    name:"", 
                    pid:"",
                    abstract:""
                },

                perPage: 3,
                currentPage: 1,
                itemss: []
            }
        },

        methods:{

            linkGen(pageNum) {
                return pageNum === 1 ? '?' : `?page=${pageNum}`
            },


            fetchSubClasses: async function(classId){
                this.classCid = classId;
                await axios.get('/api/subclasses/' + classId)
                    .then(response => (this.subClasses = response.data));

                    
                    this.subClassesList = [];
                    await this.fetchSubClassesList(classId);
                    
                    
                    if(this.subClassesList.length < 2){
                        $("#alert_of_empty").show();
                        $("#treee").hide();
                    }
                    else{
                        $("#alert_of_empty").hide();
                        $("#treee").show();
                    }
                    

                    this.$refs.tree.nodeMap = undefined;
                    this.$refs.tree.selectedNode = null;
                    this.$refs.tree.createNodeMap();      
            },

             
            fetchSubClassesList: async function(id){
                
                await axios.get('/api/getsubclasseslist/' + id)
                    .then(response => (this.subClassesList = response.data));

            },


            fetchSuperClasses: async function(classId){
                this.superClasses = [];
                this.itemss = [];

                await axios.get('/api/superclasses/' + classId)
                    .then(response => (this.superClasses = response.data));

                    await this.fetchSubClasses(this.superClasses.list[this.superClasses.list.length-1].cid);
                    this.selectedClass = this.superClasses.list[this.superClasses.list.length-1].cid;

                    this.superClasses.list.forEach(classh => {
                        this.itemss.push({Name : classh.name, Parent : this.getParentNameById(classh.pid)})
                    });         

                    var openClasses = this.superClasses.list.reverse();
                    var saveClass;
                    openClasses.forEach(classh => {

                        this.$refs.tree.createNodeMap();
                        if(this.$refs.tree.getNodeByKey((classh.cid).toString())){
                            this.$refs.tree.getNodeByKey((classh.cid).toString()).expand();
                            saveClass = this.$refs.tree.getNodeByKey((classh.cid).toString());
                        }
                        else{
                             this.$refs.tree.getNodeByKey(classh.cid).expand();
                             saveClass = this.$refs.tree.getNodeByKey((classh.cid));
                        }
                    });         

                    saveClass.select();     
            },


            fillAutoComplete: async function(){
                this.autoComplete = [];
                this.allClasses.forEach(classh => {
                    this.autoComplete.push(classh.name);
                });
            },

            findClass: function(superclassName){
                this.allClasses.forEach(classh => {
                    if(classh.name === superclassName){
                        this.fetchSuperClasses(classh.cid);
                    }
                });
            },

            fetchAllClasses: async function(){
                await axios.get('/api/getall')
                    .then(response => (this.allClasses = response.data));
                 this.fillClassSelect();
                 this.fillAutoComplete();
            },

            fillClassSelect: async function(){
                this.classSelection = [];
                 this.allClasses.forEach(classh => {
                         if(classh.pid == 0){
                             this.classSelection.push(classh);
                         }
                });
            },  

            menuItemSelected: function(item, node) {
                if (item.code === 'ADD_CHILD_NODE') {
                    const foundNode = this.allClasses.find( classh => classh.cid == node.data.id);
                    this.clearNodee();
                    this.nodee.pid = foundNode.cid;
                    $('#add_modal').modal('show');
                }
                else if (item.code === 'UPDATE_NODE') {
                    const foundNode = this.allClasses.find( classh => classh.cid == node.data.id);
                    this.nodee = foundNode;
                    $('#update_modal').modal('show');
                }
                else if (item.code === 'DELETE_NODEE') {
                    this.deleteNode(node.data.id);
                }
                else if (item.code === 'SHOW_PARENTS') {
                    this.findClass(node.data.name);
                }



            },

            getParentNameById: function(id){
                try {
                    const foundNode = this.allClasses.find( classh => classh.cid == id);
                    return foundNode.name;
                } catch (error) {
                    return '"No parent"';
                }

            },

            updateNode: function() {
                fetch('api/updateclass', {
                method: 'put',
                body: JSON.stringify(this.nodee),
                headers: {
                    'content-type': 'application/json'
                }
                })
                .then(res => res.json())
                .then(data => {
                    this.fetchSubClasses(this.classCid);
                    this.fetchAllClasses();

                    alert('Node Updated');
                    $('#update_modal').modal('hide');
                    this.clearNodee();

                })
                .catch(err => console.log(err));
            },

            addNode: function(){
                fetch('api/addclass', {
                method: 'POST',
                body: JSON.stringify(this.nodee),
                headers: {
                    'content-type': 'application/json'
                }
                })
                .then(res => this.response = res.json())
                .then(data => {
                    this.fetchSubClasses(this.classCid);
                    this.fetchAllClasses();

                    if(data.ret == "false"){
                        alert(data.message[0]);
                    }
                    else{
                        alert("\"" + this.nodee.name + "\" was successfuly added !" );
                    }

                    $('#add_modal').modal('hide');
                    this.clearNodee();
                })
                .catch(err => console.log(err));
            },

            deleteNode: function(id) {

                const foundNode = this.allClasses.find( classh => classh.cid == id);
                if (confirm('Are You Sure you want to delete "'+ foundNode.name +'" Node?')) {
                    fetch(`api/deleteclass/${id}`, {
                    method: 'delete'
                    })
                    .then(res => res.json())
                    .then(data => {
                        this.fetchSubClasses(this.classCid);
                        this.fetchAllClasses();

                        alert('Node "'+ foundNode.name +'" was Removed');
                    })
                    .catch(err => console.log(err));
                }
            },

            clearNodee: function() {
                this.nodee.cid = "";
                this.nodee.name = "";
                this.nodee.pid = "";
                this.nodee.abstract = "";
            },


            
        }

    }
</script>

<style>

.tree-branch{
    border-top: solid rgb(230, 230, 230) 1px;
    border-left: solid rgb(230, 230, 230) 1px;
    border-top-left-radius: 8px;
    padding-left: 5px;

}

.tree-node-children{
    margin-top: 5px;
}

</style>
