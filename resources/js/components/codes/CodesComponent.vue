<template>
    <div>
        <div >
            <b-row>
                <b-col>
                    <b-form-group label="Buscar por escuela:">
                        <b-form-input v-model="school" @keyup="showSchools()"
                            style="text-transform:uppercase;">
                        </b-form-input>
                        <div class="list-group" v-if="schools.length" id="listR">
                            <a 
                                href="#" v-bind:key="i" 
                                class="list-group-item list-group-item-action" 
                                v-for="(school, i) in schools" 
                                @click="selectSchool(school)">
                                {{ school.name }}
                            </a>
                        </div>
                    </b-form-group>
                </b-col>
                <b-col>
                    <b-form-group label="Buscar por libro:">
                        <b-form-input v-model="book" @keyup="showBooks()"
                            style="text-transform:uppercase;">
                        </b-form-input>
                        <div class="list-group" v-if="books.length" id="listR">
                            <a 
                                href="#" v-bind:key="i" 
                                class="list-group-item list-group-item-action" 
                                v-for="(book, i) in books" 
                                @click="selectBook(book)">
                                {{ book.name }}
                            </a>
                        </div>
                    </b-form-group>
                </b-col>
                <b-col sm="2" class="text-right">
                    <b-button :disabled="load || validar()"
                        pill variant="primary" :href="`/student/download_all/${school}`">
                        <b-icon-download></b-icon-download> Fisico y Digital (Pendiente)
                    </b-button>
                </b-col>
            </b-row>
        </div>
        <b-tabs content-class="mt-3" fill class="tab-stds">
            <b-tab title="Digital" active>
                <b-row class="mb-2">
                    <b-col>
                        <b-pagination v-model="currentPage1" pills
                            :per-page="perPage" :total-rows="digitales.length" :disabled="load">
                        </b-pagination>
                    </b-col>
                    <b-col class="text-right">
                        <b-button :disabled="load" pill variant="success" 
                            @click="modalShow = !modalShow">
                            <b-icon-plus-circle></b-icon-plus-circle> Subir codigos
                        </b-button>
                    </b-col>
                    <b-col class="text-right">
                        <b-button :disabled="load || validar()"
                            pill variant="primary" :href="`/student/download_emails/${school}/${book}/2`">
                            <b-icon-download></b-icon-download> Digital (Pendiente)
                        </b-button>
                    </b-col>
                </b-row>
                <b-table v-if="digitales.length > 0" :items="digitales" :fields="fields" class="mb-3"
                    :per-page="perPage" :current-page="currentPage1" :busy="load">
                    <template v-slot:cell(index)="data">
                        {{ data.index + 1 }}
                    </template>
                    <template v-slot:cell(school_id)="data">
                        {{ data.item.school.name }}
                    </template>
                    <template v-slot:cell(codes)="data">
                        <b-badge v-if="data.item.codes" pill variant="success">
                            <b-icon-check2-circle></b-icon-check2-circle> Enviado
                        </b-badge>
                        <b-badge v-else pill variant="secondary">
                            <b-icon-exclamation-triangle></b-icon-exclamation-triangle> No enviado
                        </b-badge>
                    </template>
                </b-table>
                <b-alert v-else show variant="dark" class="text-center">
                    <b-icon-info-circle></b-icon-info-circle> No hay registros
                </b-alert>
            </b-tab>
            <b-tab title="Fisico">
                <b-row class="mb-2">
                    <b-col>
                        <b-pagination v-model="currentPage2" pills
                            :per-page="perPage" :total-rows="fisicos.length" :disabled="load">
                        </b-pagination>
                    </b-col>
                    <b-col class="text-right">
                        Borrar seleccionado
                        <b-button @click="clearSelected" pill size="sm" 
                            variant="secondary" :disabled="load || this.selected.length == 0">
                            <b-icon-x></b-icon-x>
                        </b-button>
                    </b-col>
                    <b-col sm="4" class="text-right">
                        <b-button variant="success" pill :disabled="load || this.selected.length == 0"
                            @click="mark_delivery()">
                            <b-icon-check></b-icon-check> Marcar entrega <br> (Seleccionados)
                        </b-button>
                    </b-col>
                </b-row>
                <b-table v-if="fisicos.length > 0" :items="fisicos" :fields="fields" class="mb-3"
                    :per-page="perPage" :current-page="currentPage2" :busy="load"
                    ref="selectableTable" selectable :select-mode="selectMode"
                    @row-selected="onRowSelected">
                    <template v-slot:cell(index)="data">
                        {{ data.index + 1 }}
                    </template>
                    <template v-slot:cell(school_id)="data">
                        {{ data.item.school.name }}
                    </template>
                    <template v-slot:cell(codes)="data">
                        <b-badge v-if="data.item.delivery" pill variant="success">
                            <b-icon-check2-circle></b-icon-check2-circle> Entregado
                        </b-badge>
                        <b-badge v-else pill variant="secondary">
                            <b-icon-exclamation-triangle></b-icon-exclamation-triangle> No entregado
                        </b-badge>
                    </template>
                    <template v-slot:cell(selected)="{ rowSelected }">
                        <template v-if="rowSelected">
                            <b-icon-check-square-fill></b-icon-check-square-fill>
                        </template>
                        <template v-else>
                            <b-icon-square></b-icon-square>
                        </template>
                    </template>
                    <template #thead-top="data">
                            <b-tr>
                                <b-th colspan="4"></b-th>
                                <b-th colspan="2" class="text-right">
                                    Seleccionar todo
                                </b-th>
                                <b-th>
                                    <b-button @click="selectAllRows" pill 
                                        size="sm" variant="primary" :disabled="school == null || school == '' || school == ' '">
                                        <b-icon-check-square-fill></b-icon-check-square-fill>
                                    </b-button>
                                </b-th>
                            </b-tr>
                        </template>
                </b-table>
                <b-alert v-else show variant="dark" class="text-center">
                    <b-icon-info-circle></b-icon-info-circle> No hay registros
                </b-alert>
            </b-tab>
        </b-tabs>
        
        <b-modal v-model="modalShow" hide-footer title="Subir archivo">
            <b-alert v-if="errorFormat" show variant="warning">Formato de archivo no permitido</b-alert>
            <form @submit="onSubmit" enctype="multipart/form-data">
                <input 
                    :disabled="load" type="file" required id="archivoType"
                    class="custom-file" v-on:change="fileChange">
                <label for="archivoType"><b-icon-upload></b-icon-upload> Seleccionar archivo</label>
                <br><b>Archivo:</b> {{ file ? file.name : 'No se ha seleccionado archivo' }}
                <div class="text-right mt-3">
                    <b-button pill :disabled="load" variant="success" type="submit">
                        <b-icon-forward></b-icon-forward> Enviar
                    </b-button>
                </div>
                <b-alert class="mt-2" v-if="load" show variant="info">
                    <b-spinner small type="grow"></b-spinner> Subiendo...<br>
                    No cierre el recuadro hasta que se termine de subir el archivo.
                </b-alert>
            </form>
        </b-modal>
    </div>
</template>

<script>
// SWEETALERT
import swal from 'sweetalert';
export default {
    props: ['registers1', 'registers2'],
    data(){
        return {
            modalShow: false,
            errorFormat: '',
            load: false,
            file: null,
            digitales: this.registers1,
            fisicos: this.registers2,
            items: [],
            fields: [
                {key: 'index', label: 'N.'},
                {key: 'school_id', label: 'Escuela'},
                {key: 'book', label: 'Libro'},
                {key: 'name', label: 'Nombre'},
                {key: 'email', label: 'Correo electronico'},
                {key: 'codes', label: 'Codigo enviado'},
                {key: 'selected', label: ''}
            ], 
            school: null,
            book: null,
            schools: [],
            books: [],
            currentPage1: 1,
            currentPage2: 1,
            perPage: 50,
            selectMode: 'multi',
            selected: []
        }
    },
    methods: {
        fileChange(e){
            this.file = e.target.files[0];
        },
        onSubmit(e){
            e.preventDefault();
            var fileInput = document.getElementById('archivoType');
            var allowedExtensions = /(\.xlsx)$/i;
            this.load = true;
            if(allowedExtensions.exec(fileInput.value)){
                this.errorFormat = false;
                let formData = new FormData();
                formData.append('file', this.file);

                axios.post('/student/send_codes', formData, { headers: { 'content-type': 'multipart/form-data' } })
                .then(response => {
                    this.modalShow = false;
                    this.items = response.data;
                    // console.log(response.data);
                    swal("Guardado", "Los codigos fueron enviados correctamente.", "success");
                    this.load = false;
                }).catch(error => {
                    this.load = false;
                    swal("Error", "Ocurrio un error al enviar todos los codigos, intenta de nuevo por favor.", "error");
                });
            } else {
                this.errorFormat = true;
                this.load = false;
            }
        },
        showSchools(){
            if(this.school.length > 0 && this.school !== ' '){
                this.book = null;
                axios.get('/schools/show_schools', {params: {escuela: this.school}}).then(response => {
                    this.schools = response.data;
                }).catch(error => {
                    // PENDIENTE
                });
            } else {
                this.schools = [];
            }
        },
        showBooks(){
            if(this.book.length > 0 && this.book !== ' '){
                this.school = null;
                axios.get('/books/show_books', {params: {book: this.book}}).then(response => {
                    this.books = response.data;
                }).catch(error => {
                    // PENDIENTE
                });
            } else {
                this.books = [];
            }
        },
        selectSchool(school){
            axios.get('/schools/schools_to_email', {params: {school_id: school.id}}).then(response => {
                this.schools = [];
                this.school = school.name;
                this.digitales = response.data.digitales;
                this.fisicos = response.data.fisicos;
            }).catch(error => {
                // PENDIENTE
            });
        },
        selectBook(book){
            axios.get('/student/books_to_email', {params: {book: book.name}}).then(response => {
                this.books = [];
                this.book = book.name;
                this.digitales = response.data.digitales;
                this.fisicos = response.data.fisicos;
            }).catch(error => {
                // PENDIENTE
            });
        },
        validar(){
            let s = this.school !== null && this.school !== '' && this.school !== ' ';
            let b = this.book !== null && this.book !== '' && this.book !== ' ';
            if(s || b) return false;
            return true;
        },
        onRowSelected(items) {
            this.selected = items
        },
        selectAllRows(){
            this.$refs.selectableTable.selectAllRows()
        },
        clearSelected() {
            this.$refs.selectableTable.clearSelected()
        },
        mark_delivery(){
            this.load = true;
            let form = { school: this.school, selected: this.selected }
            axios.put('/student/mark_delivery', form).then(response => {
                this.fisicos = response.data;
                this.load = false;
            }).catch(error => {
                // PENDIENTE
                this.load = false;
            });
        }
    }
}
</script>

<style scoped>
    #listR{
        position: absolute;
        z-index: 100;
    }
</style>