<template>
    <div class="container mt-4 card p-3 bg-white" style="width: 1500px">
        <h3><center>{{"Select Dates To Get Astroid"}}</center></h3>
        <h4 class="text-danger"><center>{{errorMessage}}</center></h4>
        <div class="container card p-3 bg-white" style="width: 1000px">
            <form @submit.prevent="submit">
                <div class="row mt-2">
                    <div class="form-group col-md-6 required">
                        <label for="fromDate">From Date:<span style="color:#ff0000"> *</span></label>
                        <date-picker v-model="fromDate" valueType="format"></date-picker>
                    </div>
                    <div class="form-group col-md-6 required">
                        <label for="toDate">To Date:<span style="color:#ff0000"> *</span></label>
                        <date-picker v-model="toDate" valueType="format"></date-picker>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group mt-4 col-md-12">
                        <button class="btn btn-primary form-control" value="Submit" name="Submit"
                            id="search">Submit</button>
                    </div>
                </div>
            </form>
        </div>
        <br><br>
        <div class="container card p-3 bg-white" style="width: 1000px"  v-if="loading">
            <div class="row">
                <div class="col-md-6">
                    <h4><b>Fastest Asteroid:</b><br>Asteroid ID is {{neoData['fastestAseroidId']}}<br>Speed in KM/H = {{neoData['fastestAseroid']}}</h4><br>
                    <h4><b>Closest Asteroid:</b><br>Asteroid ID is {{neoData['closestAseroidId']}}<br>Distance = {{neoData['closestAseroid']}} </h4><br>
                    <h4><b>Average Size of the Asteroids:</b><br>In kilometers = {{neoData['average']}} </h4>
                </div>
            
                <div class="col-md-6">
                    <div style="width: 450px;height: 450px;">
                        <Bar
                            id="my-chart-id"
                            :data="chartData" :options="chartOptions"
                        />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    import DatePicker from 'vue2-datepicker';
    import 'vue2-datepicker/index.css';
    import { Bar } from 'vue-chartjs';
    import { Chart as ChartJS, Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale } from 'chart.js'

    ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale)


    export default {
        props: ['resource'],
        components: { DatePicker, Bar },
        data() {
            return {
                neoData: [],
                fromDate : null,
                toDate : null,
                loading : false,
                noOfAstroids: null,
                astroidsAppeardate: null,
                errorMessage: ''
            };
        },
        methods: {
            submit(){
                axios.post(this.resource, {
                    fromDate: this.fromDate,
                    toDate: this.toDate
                })
                .then(response => {
                    this.neoData = response.data
                    this.noOfAstroids = this.neoData['neo_count_by_date_arry_values']
                    this.astroidsAppeardate = this.neoData['neo_count_by_date_arry_keys']
                    this.loading = true
                })
                .catch(error => {
                    this.errorMessage = error.message;
                    console.error("There was an error!", error);
                });
            }
        },
        computed: {
            chartData() {
                return {
                    labels: this.astroidsAppeardate,
                    datasets: [{
                        label: '# of Asteroids',
                        data: this.noOfAstroids,
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.2)',
                            'rgba(54, 162, 235, 0.2)',
                            'rgba(255, 206, 86, 0.2)',
                            'rgba(75, 192, 192, 0.2)',
                            'rgba(153, 102, 255, 0.2)',
                            'rgba(255, 159, 64, 0.2)'
                        ],
                        borderColor: [
                            'rgba(255, 99, 132, 1)',
                            'rgba(54, 162, 235, 1)',
                            'rgba(255, 206, 86, 1)',
                            'rgba(75, 192, 192, 1)',
                            'rgba(153, 102, 255, 1)',
                            'rgba(255, 159, 64, 1)'
                        ],
                        borderWidth: 1
                    }]
                }
            },
            chartOptions() { 
                return {
                    responsive: true
                } 
            }
        }
    }
</script>