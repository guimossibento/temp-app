// resources/js/dashboard.js

import $ from 'jquery';
import Chart from 'chart.js/auto';

$(document).ready(function () {
    // Loop through each canvas element that has a data-city-id attribute
    $('canvas[data-city-id]').each(function () {
        var $canvas = $(this);
        var cityId = $canvas.data('city-id');
        // Retrieve the city name from the card header
        var cityName = $canvas.closest('.card').find('.card-header').text().trim();

        // Make an AJAX request to fetch temperature data for this city
        $.ajax({
            url: '/api/temperatures?filter[city_id]=' + cityId,
            method: 'GET',
            dataType: 'json',
            success: function (response) {
                // Assuming the API returns { data: [...] }
                var records = response.data;
                // Sort records by recorded_at timestamp (ascending)
                records.sort(function (a, b) {
                    return new Date(a.recorded_at) - new Date(b.recorded_at);
                });

                var labels = records.map(function (record) {
                    return record.recorded_at;
                });

                var dataValues = records.map(function (record) {
                    return record.value;
                });

                const ctx = $canvas[0].getContext('2d');

                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: cityName + ' Temperature',
                            data: dataValues,
                            borderColor: function(context) {
                                const chart = context.chart;
                                // If chart area is not yet available, return a default color.
                                if (!chart.chartArea) {
                                    return 'red';
                                }
                                const { top, bottom } = chart.chartArea;
                                // Create a gradient from the top to the bottom of the chart area.
                                const gradient = chart.ctx.createLinearGradient(0, top, 0, bottom);
                                // Get the pixel for the value 0 on the y scale.
                                const zeroY = chart.scales.y.getPixelForValue(0);
                                // Calculate the relative stop for value 0.
                                const stop = (zeroY - top) / (bottom - top);
                                // Set the gradient stops:
                                // Top (above 0): red
                                gradient.addColorStop(0, 'red');
                                // At 0: black
                                gradient.addColorStop(stop, 'black');
                                // Bottom (below 0): blue
                                gradient.addColorStop(1, 'blue');
                                return gradient;
                            },
                            backgroundColor: 'transparent',
                            fill: false,
                            tension: 0.1
                        }]
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Temperature (°C)'
                                }
                            }
                        }
                    }
                });
            },
            error: function (xhr, status, error) {
                console.error('Error fetching temperature data for city ' + cityId + ': ' + error);
            }
        });
    });
});
