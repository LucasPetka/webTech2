<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card mt-5">
                    <div class="card-header">Hierarchy trees</div>

                    <div class="card-body">


                        <div class="row mb-2">
                            <div class="col-12">
                                <label for="exampleFormControlSelect1">Select which class you want to see:</label>
                                <div class="input-group">
                                    <select class="form-control" id="exampleFormControlSelect1" v-model="selectedClass" v-on:change="fetchSubClasses(selectedClass)">
                                        <option v-for="classh in classSelection" v-bind:key="classh.cid" :value="classh.cid" >{{ classh.name }}</option>
                                    </select>
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-success align-bottom" data-toggle="modal" data-target="#add_modal">
                                            <i class="fas fa-plus"></i> Add Node
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="input-group mb-5">
                            <vue-bootstrap-typeahead placeholder="Search for the class by name"  v-model="superclassName" :data="autoComplete" class="w-75"/>
                            <div class="input-group-append">
                                <button class="btn btn-primary" v-on:click="findClass(superclassName)" type="button">Find</button>
                            </div>
                        </div> 


                             <div v-if="subClasses != null  && subClasses.length != 0"> 
                                <b-tree-view :data="subClasses" :contextMenuItems="items" @contextMenuItemSelect="menuItemSelected" ref="tree"></b-tree-view>
                             </div>  

                        <!-- Update Modal -->
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
                                            <label for="nameInput">Name</label>
                                            <input name="node_name" type="name" class="form-control" id="nameInput" placeholder="Enter Node name" v-model="nodee.name" required>
                                        </div>

                                        <div class="form-group">
                                            <label for="exampleFormControlSelect1">Select Parent</label>
                                            <select name="node_parent" class="form-control" id="exampleFormControlSelect1" v-model="nodee.pid" required>
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

                        <!-- Add Modal -->
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

                                    <p>Node name: {{ nodee.name }}<br>Node Parent ID: {{ nodee.pid }}</p>

                                    
                                        <div class="form-group">
                                            <label for="nameInput">Name</label>
                                            <input name="node_name" type="name" class="form-control" id="nameInput" placeholder="Enter Node name" v-model="nodee.name" required>
                                        </div>

                                        <div class="form-group">
                                            <label for="exampleFormControlSelect1">Select Parent</label>
                                            <select name="node_parent" class="form-control" id="exampleFormControlSelect1" v-model="nodee.pid" required>
                                            <option :value="0" >**Super Parent**</option>
                                            <option v-for="classh in allClasses" v-bind:key="classh.cid" :value="classh.cid" >{{ classh.name }}</option>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label for="exampleFormControlSelect1">Abstract class</label>
                                            <select name="node_parent" class="form-control" id="exampleFormControlSelect1" v-model="nodee.abstract" required>
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
            </div>
        </div>
    </div>
</template>

<script>

    export default {
        mounted() {
            this.fetchAllClasses();

        },

        data() {
            return {
                //Class cid which loaded right now
                classCid:null,

                //Subclasses that are printed by BOOTSTRAP tree
                subClasses: [],

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
                items: [ { code: 'DELETE_NODEE', label: 'Delete node' },{ code: 'ADD_CHILD_NODE', label: 'Add child' }, { code: 'UPDATE_NODE', label: 'Update node' } ],
                response: [],
                //selected node by update or delete
                nodee:{
                    cid:"",
                    name:"", 
                    pid:"",
                    abstract:""
                },
            }
        },

        methods:{


            fetchSubClasses: async function(classId){
                this.classCid = classId;
                await axios.get('/api/subclasses/' + classId)
                    .then(response => (this.subClasses = response.data));

                    this.$refs.tree.selectedNode = null;
                    this.$refs.tree.createNodeMap();        //testing class refresh
                
                    
                if(this.$refs.tree){
                 console.log(this.$refs.tree);  
                }

            },

            fetchSuperClasses: async function(classId){
                await axios.get('/api/superclasses/' + classId)
                    .then(response => (this.superClasses = response.data));

                    var openClasses = this.superClasses.list.reverse();
                    var saveClass;
                    openClasses.forEach(classh => {
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
                 this.allClasses.forEach(classh => {
                         if(classh.pid == 0){
                             this.classSelection.push(classh);
                         }
                });
            },

            menuItemSelected: function(item, node) {
                if (item.code === 'ADD_CHILD_NODE') {
                    //this.clearNodee();
                    const foundNode = this.allClasses.find( classh => classh.cid == node.data.id);
                    this.nodee.cid = foundNode.cid;
                    this.nodee.name = foundNode.name;
                    this.nodee.pid = foundNode.pid;
                    $('#add_modal').modal('show');
                }
                else if (item.code === 'UPDATE_NODE') {
                    //this.clearNodee();
                    const foundNode = this.allClasses.find( classh => classh.cid == node.data.id);
                    this.nodee = foundNode;
                    $('#update_modal').modal('show');
                }
                else if (item.code === 'DELETE_NODEE') {
                    this.deleteNode(node.data.id);
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
                    alert('Node Updated');
                    $('#update_modal').modal('hide');
                    this.clearNodee();

                })
                .catch(err => console.log(err));
            },

            addNode: function(){
                fetch('api/addclass', {
                method: 'post',
                body: JSON.stringify(this.nodee),
                headers: {
                    'content-type': 'application/json'
                }
                })
                .then(res => this.response = res.json())
                .then(data => {
                    this.fetchSubClasses(this.classCid);
                    alert(data.message[0]);
                    $('#add_modal').modal('hide');
                    this.clearNodee();
                })
                .catch(err => console.log(err));
            },

            deleteNode: function(id) {
                if (confirm('Are You Sure you want to delete this Node?')) {
                    fetch(`api/deleteclass/${id}`, {
                    method: 'delete'
                    })
                    .then(res => res.json())
                    .then(data => {
                        this.fetchSubClasses(this.classCid);
                        alert('Node Removed');
                    })
                    .catch(err => console.log(err));
                }
            },

            clearNodee() {
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
