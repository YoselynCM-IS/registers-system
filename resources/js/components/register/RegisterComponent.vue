<template>
    <div>
        <b-form @submit.prevent="onContinue()">
            <b-form-group label="Tipo de pago:">
                <b-form-select v-model="type" :disabled="part2"
                    :options="types" required @change="onType()"
                ></b-form-select>
            </b-form-group>
            <div v-if="type !== null">
                <b-row>
                    <b-col>
                        <b-form-group label="Folio">
                            <b-form-input v-model="search.folio" :disabled="part2"
                                required
                            ></b-form-input>
                        </b-form-group>
                    </b-col>
                    <b-col>
                        <b-form-group v-if="type == 'deposito'" label="Autorización">
                            <b-form-input v-model="search.auto" :disabled="part2"
                                required style="text-transform:uppercase;"
                            ></b-form-input>
                        </b-form-group>
                    </b-col>
                </b-row>
                <b-row>
                    <b-col>
                        <b-form-group label="Total depositado">
                            <b-form-input v-model="search.total"
                                required type="number" :disabled="part2"
                            ></b-form-input>
                        </b-form-group>
                    </b-col>
                    <b-col>
                        <b-form-group label="Fecha del pago">
                            <b-form-datepicker :disabled="part2" v-model="search.date"></b-form-datepicker>
                        </b-form-group>
                    </b-col>
                </b-row>
            </div>
            <div v-if="!part2" class="text-right mt-3">
                <b-button pill :disabled="load" 
                    type="submit" variant="success">
                    <b-icon-arrow-right></b-icon-arrow-right> Continuar
                </b-button>
            </div>
        </b-form>
        <div v-if="part2" class="mt-5">
            <form @submit="onSubmit" enctype="multipart/form-data">   
                <h5><b>Datos del alumno</b></h5>
                <b-row>
                    <b-col>
                        <b-form-group label="Nombre">
                            <b-form-input v-model="form.name" :disabled="load"
                                style="text-transform:uppercase;" required
                            ></b-form-input>
                        </b-form-group>
                    </b-col>
                    <b-col>
                        <b-form-group label="Apellidos">
                            <b-form-input v-model="form.lastname" :disabled="load"
                                style="text-transform:uppercase;" required
                            ></b-form-input>
                        </b-form-group>
                    </b-col>
                </b-row>
                <b-form-group label="Correo electrónico:">
                    <b-form-input v-model="form.email"
                        type="email" required :disabled="load"
                    ></b-form-input>
                </b-form-group>
                <b-form-group label="Plantel:">
                    <b-form-select v-model="form.school"
                        :options="schools"
                        required :disabled="load"
                    ></b-form-select>
                </b-form-group>
                <hr>
                <h5><b>Datos del libro</b></h5>
                <b-form-group label="Libro (Titulo completo):">
                    <b-form-input v-model="form.book" :disabled="load"
                        style="text-transform:uppercase;" required
                    ></b-form-input>
                </b-form-group>
                <b-row>
                    <b-col>
                        <b-form-group label="Numero de piezas">
                            <b-form-input v-model="form.quantity"
                                required type="number" :disabled="load"
                            ></b-form-input>
                        </b-form-group>
                    </b-col>
                    <b-col>
                        <b-form-group label="Precio del libro">
                            <b-form-input v-model="form.price"
                                required type="number" :disabled="load"
                            ></b-form-input>
                        </b-form-group>
                    </b-col>
                </b-row>
                <hr>
                <h5><b>Datos del pago</b></h5>
                <b-form-group label="Plaza o sucursal donde se realizo el pago:">
                    <b-form-input v-model="form.plaza" :disabled="load"
                        style="text-transform:uppercase;" required
                    ></b-form-input>
                </b-form-group>
                <b-form-group label="Subir comprobante de pago (Formato imagen o pdf)">
                    <input 
                        :disabled="load" type="file" id="archivoType"
                        class="custom-file" v-on:change="fileChange" required>
                    <label><b>Comprobante de pago:</b> {{ form.file ? form.file.name : '' }}</label>
                </b-form-group>
                <hr>
                <b-row class="mt-3">
                    <b-col>
                        <b-alert class="mt-2" v-if="load" show variant="info">
                            <b-spinner small type="grow"></b-spinner> Guardando...<br>
                            Estamos guardando tus datos, porfavor cierres esta pagina hasta que terminemos.
                        </b-alert>
                    </b-col>
                    <b-col class="text-right" sm="3">
                        <b-button pill :disabled="load" variant="success" type="submit">
                            <i class="fa fa-plus-circle"></i> Guardar
                        </b-button>
                    </b-col>
                </b-row>
            </form>
        </div>
        
    </div>
</template>

<script>
// SWEETALERT
import swal from 'sweetalert';
export default {
    props: ['registers'],
    data(){
        return {
            search: {
                folio: '', auto: '',
                total: null, date: ''
            },
            part2: false,
            form: {
                folio_id: null,
                type: null, folio: '',
                auto: '', total: null, date: null,
                name: '', lastname: '', email: '',
                school: null, book: '',
                quantity: null, price: null,
                plaza: '', file: null
            },
            type: null,
            load: false,
            schools: [],
            optSchools: this.registers,
            types: [
                { value: null, text: 'Selecciona una opción', disabled: true },
                { value: 'deposito', text: 'DEPOSITO'},
                { value: 'tranferencia', text: 'TRANSFERENCIA'}
            ]
        }
    },
    created: function(){
        this.schools.push({
            value: null,
            text: 'Selecciona una opción',
            disabled: true
        });
        this.optSchools.forEach(school => {
            this.schools.push({
                value: school.id,
                text: `${school.name}`
            });
        });
    },
    methods: {
        fileChange(e){
            this.form.file = e.target.files[0];
        },
        onSubmit(e){
            e.preventDefault();
            var fileInput = document.getElementById('archivoType');
            var allowedExtensions = /(\.jpg|\.jpeg|\.pdf|\.png)$/i;
            this.load = true;
            if(allowedExtensions.exec(fileInput.value)){
                let fd = this.attributes();
                axios.post('/student/save_student', fd, { headers: { 'content-type': 'multipart/form-data' } }).then(response => {
                    swal("Guardado", "Tus datos han sido guardados correctamente. Gracias.", "success");
                    this.inicializar();
                    this.load = false;
                }).catch(error => {
                    // PENDIENTE
                    this.load = false;
                });
            } else {
                this.load = false;
                swal("Revisar formato de archivo", "Formato de archivo no permitido.", "warning");
            }
        },
        onContinue(){
            axios.post('/folios/get_folio', this.search).then(response => {
                console.log(response.data);
                if(response.data == 'NO EXISTE'){
                    swal("Datos incorrectos", "Los datos son incorrectos. Revisa y vuelve a intentarlo.", "error");
                } else {
                    if(!response.data.occupied){
                        this.part2 = true;
                        this.form.folio_id = response.data.id;
                        console.log(this.form.folio_id);
                    } else {
                        swal("Folio registrado", "El numero de folio ya ha sido registrado.", "warning");
                    }
                }
            }).catch(error => {
                // PENDIENTE
            });
        },
        attributes(){
            let formData = new FormData();
            formData.append('type', this.type);
            formData.append('folio', this.search.folio);
            formData.append('auto', this.search.auto);
            formData.append('total', this.search.total);
            formData.append('date', this.search.date);
            formData.append('folio_id', this.form.folio_id);
            formData.append('name', this.form.name);
            formData.append('lastname', this.form.lastname);
            formData.append('email', this.form.email);
            formData.append('school', this.form.school);
            formData.append('book', this.form.book);
            formData.append('quantity', this.form.quantity);
            formData.append('price', this.form.price);
            formData.append('plaza', this.form.plaza);
            formData.append('file', this.form.file);
            return formData;
        },
        onType(){
            this.search = { folio: '', auto: '', total: null, date: '' };
        },
        inicializar(){
            this.onType();
            this.part2 = false;
            this.form = {
                folio_id: null,
                type: null, folio: '',
                auto: '', total: null, date: null,
                name: '', lastname: '', email: '',
                school: null, book: '',
                quantity: null, price: null,
                plaza: '', file: null
            };
            this.type = null;
        }
    }
}
</script>