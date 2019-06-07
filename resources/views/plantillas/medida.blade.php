<div class="row wow fadeIn">
    <div class="col-md-12 mb-12">
        <div class="card mb-12 border-shadow">
            <div class="card-header text-left">
                Catálogo de Medidas
            </div>
            <div class="card-body">
                <!-- <canvas id="pieChart"></canvas> -->
                <form id="addMedida" class="Fdata">
                    <div class="form-group col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">
                        <label for="nombreP">Nombre: </label>
                        <input type="text" class="form-control" name="nombreP" id="nombreP">
                    </div>
                    <button type="submit" class="btn btn-primary">Ingresar</button>
                </form>
                <div id="snoAlertBox" class="alert alert-warning" data-alert="alert">Medida Almacenada.</div>
                <div id="snoAlertBoxE" class="alert alert-danger" data-alert="alert">Error al ingresar la Medida.</div>
            
                <div class="container">
                    <table class="table table-striped display table-bordered tData" id="tMedida" style="width:100%">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>