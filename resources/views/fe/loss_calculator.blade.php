<section id="loss_calculator" class="loss_calculator section light-background">

    <!-- Section Title -->
    <div class="container section-title" data-aos="fade-up">
        <span>Splitter Loss Calculator<br></span>
        <h1 class="fw-bold">Splitter Loss Calculator</h1>
        <p>Calculate the loss of a splitter using the formula.</p>
    </div>
    <!-- End Section Title -->

    <div class="container" data-aos="fade-up" data-aos-delay="100">

        <div class="row">
            <!-- Left Side Form -->
            <div class="col-md-6">
                <form>
                    <div class="mb-4">
                        <label for="inputLoss" class="form-label">Input Loss (dB)</label>
                        <div class="input-group">
                            <button class="btn btn-outline-secondary dropdown-toggle fw-bold" type="button"
                                data-bs-toggle="dropdown" aria-expanded="false" id="plusMinusBtn">
                                +/-
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item" onclick="setSign('+')">+</a>
                                </li>
                                <li>
                                    <a class="dropdown-item" onclick="setSign('-')">-</a>
                                </li>
                            </ul>
                            <input type="number" class="form-control" id="inputLoss" placeholder="7.00"
                                step="any" />
                        </div>
                        <div class="form-text">
                            Isikan angka input loss (dB) terlebih dahulu, kemudian pilih tanda <span
                                class="text-danger fw-bold">+</span> atau <span class="text-danger fw-bold">-</span>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="splitter" class="form-label">Splitter Type</label>
                        <select class="form-select" id="splitter">
                            <option selected disabled>
                                Select a Ratio Splitter
                            </option>
                            <option value="3.25">1:2</option>
                            <option value="7.00">1:4</option>
                            <option value="10.00">1:8</option>
                            <option value="13.50">1:16</option>
                            <option value="17.00">1:32</option>
                            <option value="20.00">1:64</option>
                        </select>
                        <div class="form-text">
                            Choose your splitter configuration
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="cableType" class="form-label">Cable Type</label>
                        <select class="form-select" id="cableType">
                            <option value="0.3" selected>Dropcore (0.3 dB/km)</option>
                            <option value="0.2">Patchcord (0.2 dB/km)</option>
                        </select>
                        <div class="form-text">
                            Select the type of fiber optic cable used.
                            <span id="lossPerKmInfo" class="badge bg-dark ms-2">0.3 dB/km</span>
                        </div>
                    </div>


                    <div class="mb-4">
                        <label for="cableLength" class="form-label">
                            Cable Length
                            <span id="meterDisplay" class="badge bg-primary ms-2">1000 m</span>
                            <span id="kmDisplay" class="badge bg-success ms-2">1.00 km</span>
                        </label>
                        <input type="range" class="form-range" id="cableLength" min="1" max="10000"
                            step="1" value="1000" />
                        <div class="range-value">
                            <span>1 m</span>
                            <span>10.000 m</span>
                        </div>
                        <div class="input-group mt-2">
                            <span class="input-group-text fw-bold">m</span>
                            <input type="number" class="form-control" id="cableLengthInput" min="1"
                                max="10000" value="1000" />
                        </div>
                        <div class="form-text">
                            Adjust fiber optic cable length. 1 km = 1000 m.
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="spliceLoss" class="form-label">Splice Loss (dB)</label>
                        <div class="input-group">
                            <span class="input-group-text fw-bold">-</span>
                            <input type="number" class="form-control" id="spliceLoss" placeholder="0.1"
                                step="any" />
                        </div>
                    </div>

                    <div class="mb-4" style="display: none">
                        <label for="connectorLoss" class="form-label">Connector Loss (dB)</label>
                        <div class="input-group">
                            <span class="input-group-text fw-bold">-</span>
                            <input type="number" class="form-control" id="connectorLoss" placeholder="0.1"
                                step="any" />
                        </div>
                    </div>
                </form>
            </div>

            <!-- Right Side Output -->
            <div class="col-md-6">
                <div class="green-box">
                    <h4 class="text-black">Total Loss</h4>
                    <h1 id="resultLoss" class="fw-bold text-black">-</h1>
                    <p>Total calculated optical loss</p>

                    <hr />

                    <h5 class="text-black">Status</h5>
                    <h1 class="text-warning fw-bold" id="statusText">
                        Waiting for input...
                    </h1>
                    <div class="mt-4">
                        <h5 class="mb-3 text-black">Splitter Information</h5>
                        <div class="table-responsive">
                            <table
                                class="table table-striped table-dark text-center align-middle rounded overflow-hidden">
                                <thead class="table-success text-dark">
                                    <tr>
                                        <th>Splitter</th>
                                        <th>Redaman (dB)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>1:2</td>
                                        <td>3.25</td>
                                    </tr>
                                    <tr>
                                        <td>1:4</td>
                                        <td>7.00</td>
                                    </tr>
                                    <tr>
                                        <td>1:8</td>
                                        <td>10.00</td>
                                    </tr>
                                    <tr>
                                        <td>1:16</td>
                                        <td>13.50</td>
                                    </tr>
                                    <tr>
                                        <td>1:32</td>
                                        <td>17.00</td>
                                    </tr>
                                    <tr>
                                        <td>1:64</td>
                                        <td>20.00</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
