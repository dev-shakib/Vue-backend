<template>
  <section class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Slider lists</h3>

              <div class="card-tools">
                <!-- <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#addProduct">
                      <i class="fa fa-plus-square"></i>
                      Add New
                  </button> -->
                <!-- Button trigger modal -->
                <button
                  type="button"
                  class="btn btn-primary"
                  data-toggle="modal"
                  data-target="#exampleModal"
                  @click="newModal"
                >
                  Create slider
                </button>
              </div>
            </div>
            <!-- /.card-header -->
            <div class="card-body table-responsive p-0">
              <table class="table table-hover">
                <thead>
                  <tr>
                    <th>Image</th>
                    <th>Title</th>
                    <!-- <th>Sart Price</th> -->
                    <th>Action url</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="slider in sliders" :key="slider.id">
                    <td>
                      <img
                        :src="slider.image"
                        :alt="slider.title"
                        width="100px"
                      />
                    </td>
                    <td>{{ slider.title | strippedContent }}</td>
                    <td>{{ slider.action }}</td>
                    <td>
                      <a href="#" @click="editModal(slider)">
                        <i class="fa fa-edit blue"></i>
                      </a>
                      /
                      <a href="#" @click="deleteSlider(slider.id)">
                        <i class="fa fa-trash red"></i>
                      </a>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
            <!-- /.card-body -->
          </div>
          <!-- /.card -->
        </div>
      </div>

      <!-- Modal -->
      <!-- Modal -->
      <div
        class="modal fade"
        id="exampleModal"
        tabindex="-1"
        role="dialog"
        aria-labelledby="exampleModalLabel"
        aria-hidden="true"
      >
        <div class="modal-dialog modal-lg" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
              <button
                type="button"
                class="close"
                data-dismiss="modal"
                aria-label="Close"
              >
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <form @submit.prevent="editmode ? updateSlide() : createSlide()">
                <div class="form-group">
                  <label for="exampleInputFile">File input</label>
                  <div class="input-group">
                    <div class="custom-file">
                      <input
                        type="file"
                        class="custom-file-input"
                        id="exampleInputFile"
                        @change="handleImage"
                        name="image"
                        :class="{ 'is-invalid': form.errors.has('image') }"
                      />
                      <label class="custom-file-label" for="exampleInputFile"
                        >Choose file</label
                      >
                    </div>
                  </div>
                       <has-error :form="form" field="image"></has-error>
                </div>

                <div class="form-group">
                  <label for="exampleInputEmail1">Hot Title</label>
                  <ckeditor :editor="editor" v-model="form.title"
                  :class="{ 'is-invalid': form.errors.has('title') }"
                  ></ckeditor>
                   <has-error :form="form" field="title"></has-error>
                </div>

                <div class="form-group">
                  <label for="exampleInputEmail1">Action Url</label>
                  <input
                    type="text"
                    class="form-control"
                    value=""
                    v-model="form.action"
                    :class="{ 'is-invalid': form.errors.has('action') }"
                  />
                  <has-error :form="form" field="action"></has-error>
                </div>
                <div class="modal-footer">
                  <button
                    type="button"
                    class="btn btn-secondary"
                    data-dismiss="modal"
                  >
                    Close
                  </button>
                  <button
                    v-show="editmode"
                    type="submit"
                    class="btn btn-success"
                  >
                    Update
                  </button>
                  <button
                    v-show="!editmode"
                    type="submit"
                    class="btn btn-primary"
                  >
                    Create
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script>
import ClassicEditor from "@ckeditor/ckeditor5-build-classic";
export default {
  data() {
    return {
      editor: ClassicEditor,
      editmode: false,
      sliders: {},
      form: new Form({
        id: "",
        image: "",
        title: "",
        action: "",
      }),
    };
  },
  computed: {},
  methods: {
    handleImage(event) {
      let file = event.target.files[0];
      let render = new FileReader();
      render.onloadend = (file) => {
        console.log(render.result);
        this.form.image = render.result;
      };
      render.readAsDataURL(file);
    },
    loadSliders() {
      axios.get("api/slider").then((data) => {
        this.sliders = data.data;
        // console.log(this.sliders);
      });
    },
    createSlide() {
      // console.log(this.form);
      this.$Progress.start();
      this.form
        .post("api/slider")
        .then((data) => {
          if (data.status == 200) {
            $("#exampleModal").modal("hide");
            this.form.reset();
            Toast.fire({
              icon: "success",
              title: data.data.message,
            });
            this.loadSliders();
            this.$Progress.finish();
          } else {
            Toast.fire({
              icon: "error",
              title: "Some error occured! Please try again",
            });

            this.$Progress.failed();
          }
        })
        .catch(() => {
          Toast.fire({
            icon: "error",
            title: "Some error occured! Please try again",
          });
        });
    },
    editModal(slider) {
      this.editmode = true;
      this.form.reset();
      $("#exampleModal").modal("show");
      // this.form.fill(slider);
      this.form.id = slider.id;
      this.form.title = slider.title;
      this.form.action = slider.action;
    },
    newModal() {
      this.editmode = false;
      this.form.reset();
      $("#addNew").modal("show");
    },
    updateSlide() {
      console.log(this.form);
      this.$Progress.start();
      this.form
        .put("api/slider/" + this.form.id)
        .then((data) => {
          if (data.status == 200) {
            $("#exampleModal").modal("hide");
            this.form.reset();
            Toast.fire({
              icon: "success",
              title: data.data.message,
            });
            this.loadSliders();
            this.$Progress.finish();
          } else {
            Toast.fire({
              icon: "error",
              title: "Some error occured! Please try again",
            });

            this.$Progress.failed();
          }
        })
        .catch(() => {
          Toast.fire({
            icon: "error",
            title: "Some error occured! Please try again",
          });
        });
    },
    deleteSlider(id) {
      Swal.fire({
        title: "Are you sure?",
        text: "You won't be able to revert this!",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Yes, delete it!",
      }).then((result) => {
        // Send request to the server
        if (result.value) {
          this.form
            .delete("api/slider/" + id)
            .then(() => {
              Swal.fire("Deleted!", "Your file has been deleted.", "success");
              // Fire.$emit('AfterCreate');
              this.loadSliders();
            })
            .catch((data) => {
              Swal.fire("Failed!", data.message, "warning");
            });
        }
      });
    },
  },
  created() {
    this.loadSliders();
  },
  filters: {
    truncate: function (text, length, suffix) {
      return text.substring(0, length) + suffix;
    },
    strippedContent: function (string) {
      return string.replace(/<\/?[^>]+>/gi, " ");
    },
  },
};
</script>
