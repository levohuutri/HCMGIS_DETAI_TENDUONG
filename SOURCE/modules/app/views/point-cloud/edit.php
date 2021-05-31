<?php

use app\modules\app\APPConfig;
use app\modules\app\PathConfig;
use app\modules\cms\CMSConfig;
use app\modules\contrib\gxassets\GxLaddaAsset;
use app\modules\contrib\gxassets\GxLeafletAsset;
use app\modules\contrib\gxassets\GxVueBootstrapTypeaheadAsset;

GxVueBootstrapTypeaheadAsset::register($this);
GxLaddaAsset::register($this);
GxLeafletAsset::register($this);
$pageData = [
    'pageTitle' => 'Edit point cloud informations',
    'headerElements' => [],
];
?>
<?= $this->render(PathConfig::getAppViewPath('tagPageHeader'), $pageData); ?>

<style>
    .file-upload-wrap {
        height: 350px;
        background: rgba(0, 0, 0, .2);
        border-radius: .1875rem;
        border: 1.5px dashed rgba(0, 0, 0, .3);
    }

    .delete-file:hover {
        opacity: .8;
    }

    .thumbnail-wrap img, .pointfile-wrap img {
        max-height: 50%;
        max-width: 90%;
        object-fit: contain;
    }

    .map-widget {
        height: 500px;
    }
</style>

<div class="container" id="pointcloud-upload-page">
    <div class="page-content d-flex justify-content-center">
        <form class="py-3" id="pointcloud-upload-form">
            <div class="row">
                <div class="col-md-12">
                    <h4 class="font-weight-bold text-center mt-3 mb-4">Point cloud information</h4>
                </div>
                <div class="col-md-6 form-group">
                    <label for="title">Title <span class="text-danger">*</span></label>
                    <input type="text" name="PointCloud[title]" class="form-control" v-model="title">
                </div>
                <div class="col-md-6 form-group">
                    <label for="tags">Tag list</label>
                    <input type="text" name="PointCloud[tags]" class="form-control" placeholder="Separated by commas">
                </div>
                <div class="col-md-6 form-group">
                    <label for="description">Description</label>
                    <textarea type="text" name="PointCloud[description]" class="form-control" rows="3" v-model="description"></textarea>
                </div>
                <div class="col-md-6 form-group">
                    <label for="type">Publish type</label>
                    <select name="PointCloud[type]" class="form-control">
                        <option value="1">Public</option>
                        <option value="0">Private</option>
                    </select>
                </div>
                <div class="col-md-6 form-group">
                    <label for="tags">Collectors</label>
                    <input type="text" name="PointCloud[collectors]" class="form-control">
                </div>
                <div class="col-md-6 form-group">
                    <label for="tags">Reference</label>
                    <input type="text" name="PointCloud[reference]" class="form-control">
                </div>
                <div class="col-md-6 form-group">
                    <label for="thumbnail">Thumbnail <span class="text-danger">*</span></label>
                    <div class="file-upload-wrap p-3" v-cloak>
                        <div class="file-upload h-100 d-flex flex-column justify-content-center align-items-center position-relative" v-if="!chooseThumbnail">
                            <h4 class="mb-0 file-upload-title">Drop a thumbnail for your point cloud</h4>
                            <h5>(jpg, jpeg or png)</h5>
                            <input type="file" name="thumbnail" accept=".jpg, .jpeg, .png" class="position-absolute top-0 w-100 h-100 opacity-0 cursor-pointer input-thumbnail" @change="readFileInfo">
                        </div>
                        <div class="file-uploaded thumbnail-wrap h-100 d-flex flex-column justify-content-center align-items-center" v-else>
                            <img class="thumbnail" src="#" alt="Point cloud">
                            <h6 class="file-name thumbnail-name my-2"></h6>
                            <div class="d-flex position-relative my-2 w-50">
                                <div class="progress progress-thumbnail w-100" style="height: 1rem;" v-if="progressUploadThumbnail >= 0">
                                    <div class="progress-bar progress-bar-striped bg-primary" :style="'width: ' + progressUploadThumbnail + '%'">
                                        <span>{{ progressUploadThumbnail }}% Complete</span>
                                    </div>
                                </div>
                                <i class="icon-checkmark2 text-primary position-absolute" style="right: -20px" v-if="progressUploadThumbnail >= 100"></i>
                            </div>
                            <!-- <h6 class="my-3 error-message text-danger" v-if="progressUploadThumbnail < 0">
                                Something went wrong, <a href="#" @click="reupThumbnail">Re-upload</a>
                            </h6> -->
                            <h5 class="delete-file cursor-pointer text-danger" v-if="progressUploadThumbnail < 0 || progressUploadThumbnail >= 100" @click="removeThumbnail">
                                <i class="icon-cancel-circle2 mr-2"></i>Delete thumbnail
                            </h5>
                        </div>
                        <input type="hidden" name="PointCloud[thumbnail]" v-model="thumbnail">
                    </div>
                </div>
                <div class="col-md-6 form-group">
                    <label for="pointcloud-file">Point cloud file <span class="text-danger">*</span></label>
                    <div class="file-upload-wrap p-3" v-cloak>
                        <div class="file-upload h-100 d-flex flex-column justify-content-center align-items-center position-relative" v-if="!choosePointFile">
                            <h4 class="mb-0 file-upload-title">
                                Drop your point cloud file
                            </h4>
                            <h5>(las, laz, ptx, ply, rar or zip)</h5>
                            <input type="file" name="pointcloud" accept=".las, .laz, .ptx, .ply, .rar, .zip" class="position-absolute top-0 w-100 h-100 opacity-0 cursor-pointer input-pointfile" @change="readPointInfo">
                        </div>
                        <div class="file-uploaded pointfile-wrap h-100 d-flex flex-column justify-content-center align-items-center position-relative" v-else>
                            <img src="data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiIHN0YW5kYWxvbmU9Im5vIj8+CjwhLS0gQ3JlYXRlZCB3aXRoIElua3NjYXBlIChodHRwOi8vd3d3Lmlua3NjYXBlLm9yZy8pIC0tPgoKPHN2ZwogICB4bWxuczpkYz0iaHR0cDovL3B1cmwub3JnL2RjL2VsZW1lbnRzLzEuMS8iCiAgIHhtbG5zOmNjPSJodHRwOi8vY3JlYXRpdmVjb21tb25zLm9yZy9ucyMiCiAgIHhtbG5zOnJkZj0iaHR0cDovL3d3dy53My5vcmcvMTk5OS8wMi8yMi1yZGYtc3ludGF4LW5zIyIKICAgeG1sbnM6c3ZnPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIKICAgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIgogICB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIKICAgeG1sbnM6c29kaXBvZGk9Imh0dHA6Ly9zb2RpcG9kaS5zb3VyY2Vmb3JnZS5uZXQvRFREL3NvZGlwb2RpLTAuZHRkIgogICB4bWxuczppbmtzY2FwZT0iaHR0cDovL3d3dy5pbmtzY2FwZS5vcmcvbmFtZXNwYWNlcy9pbmtzY2FwZSIKICAgd2lkdGg9IjIwMCIKICAgaGVpZ2h0PSIyMDAiCiAgIHZpZXdCb3g9IjAgMCAyMDAgMjAwIgogICBpZD0ic3ZnNDE5MyIKICAgdmVyc2lvbj0iMS4xIgogICBpbmtzY2FwZTp2ZXJzaW9uPSIwLjkxIHIxMzcyNSIKICAgc29kaXBvZGk6ZG9jbmFtZT0iY2xvdWQuc3ZnIj4KICA8ZGVmcwogICAgIGlkPSJkZWZzNDE5NSI+CiAgICA8bGluZWFyR3JhZGllbnQKICAgICAgIGlua3NjYXBlOmNvbGxlY3Q9ImFsd2F5cyIKICAgICAgIGlkPSJsaW5lYXJHcmFkaWVudDQ5MjciPgogICAgICA8c3RvcAogICAgICAgICBzdHlsZT0ic3RvcC1jb2xvcjojZmZmZmZmO3N0b3Atb3BhY2l0eToxOyIKICAgICAgICAgb2Zmc2V0PSIwIgogICAgICAgICBpZD0ic3RvcDQ5MjkiIC8+CiAgICAgIDxzdG9wCiAgICAgICAgIHN0eWxlPSJzdG9wLWNvbG9yOiNjY2NjY2M7c3RvcC1vcGFjaXR5OjEiCiAgICAgICAgIG9mZnNldD0iMSIKICAgICAgICAgaWQ9InN0b3A0OTMxIiAvPgogICAgPC9saW5lYXJHcmFkaWVudD4KICAgIDxsaW5lYXJHcmFkaWVudAogICAgICAgaW5rc2NhcGU6Y29sbGVjdD0iYWx3YXlzIgogICAgICAgeGxpbms6aHJlZj0iI2xpbmVhckdyYWRpZW50NDkyNyIKICAgICAgIGlkPSJsaW5lYXJHcmFkaWVudDQ5MzMiCiAgICAgICB4MT0iMTI2LjUyMTYxIgogICAgICAgeTE9IjkyOC44NzEwMyIKICAgICAgIHgyPSIyNy43NzkxOTgiCiAgICAgICB5Mj0iOTg0LjQyOTM4IgogICAgICAgZ3JhZGllbnRVbml0cz0idXNlclNwYWNlT25Vc2UiCiAgICAgICBncmFkaWVudFRyYW5zZm9ybT0idHJhbnNsYXRlKDAsMTgpIiAvPgogICAgPHBhdHRlcm4KICAgICAgIHBhdHRlcm5Vbml0cz0idXNlclNwYWNlT25Vc2UiCiAgICAgICB3aWR0aD0iOC42NDI4NTcxIgogICAgICAgaGVpZ2h0PSI4LjY0Mjg1NzEiCiAgICAgICBwYXR0ZXJuVHJhbnNmb3JtPSJ0cmFuc2xhdGUoMjguMzA0NTc5LDEwMTkuNDU1MikiCiAgICAgICBpZD0icGF0dGVybjYyMjMiPgogICAgICA8Y2lyY2xlCiAgICAgICAgIHI9IjMuODIxNDI4NSIKICAgICAgICAgY3k9IjQuMzIxNDI4MyIKICAgICAgICAgY3g9IjQuMzIxNDI4MyIKICAgICAgICAgc3R5bGU9ImZpbGw6I2NjY2NjYztzdHJva2U6IzAwMDAwMDtzdHJva2Utb3BhY2l0eToxIgogICAgICAgICBpZD0icGF0aDYyMjEiIC8+CiAgICA8L3BhdHRlcm4+CiAgPC9kZWZzPgogIDxzb2RpcG9kaTpuYW1lZHZpZXcKICAgICBpZD0iYmFzZSIKICAgICBwYWdlY29sb3I9IiNmZmZmZmYiCiAgICAgYm9yZGVyY29sb3I9IiM2NjY2NjYiCiAgICAgYm9yZGVyb3BhY2l0eT0iMS4wIgogICAgIGlua3NjYXBlOnBhZ2VvcGFjaXR5PSIwLjAiCiAgICAgaW5rc2NhcGU6cGFnZXNoYWRvdz0iMiIKICAgICBpbmtzY2FwZTp6b29tPSIyLjgiCiAgICAgaW5rc2NhcGU6Y3g9IjM0LjE1NjM0NyIKICAgICBpbmtzY2FwZTpjeT0iMTEwLjQwMjMiCiAgICAgaW5rc2NhcGU6ZG9jdW1lbnQtdW5pdHM9InB4IgogICAgIGlua3NjYXBlOmN1cnJlbnQtbGF5ZXI9ImxheWVyMSIKICAgICBzaG93Z3JpZD0iZmFsc2UiCiAgICAgdW5pdHM9InB4IgogICAgIGlua3NjYXBlOndpbmRvdy13aWR0aD0iMTI4MCIKICAgICBpbmtzY2FwZTp3aW5kb3ctaGVpZ2h0PSI5NzIiCiAgICAgaW5rc2NhcGU6d2luZG93LXg9IjAiCiAgICAgaW5rc2NhcGU6d2luZG93LXk9IjAiCiAgICAgaW5rc2NhcGU6d2luZG93LW1heGltaXplZD0iMSIgLz4KICA8bWV0YWRhdGEKICAgICBpZD0ibWV0YWRhdGE0MTk4Ij4KICAgIDxyZGY6UkRGPgogICAgICA8Y2M6V29yawogICAgICAgICByZGY6YWJvdXQ9IiI+CiAgICAgICAgPGRjOmZvcm1hdD5pbWFnZS9zdmcreG1sPC9kYzpmb3JtYXQ+CiAgICAgICAgPGRjOnR5cGUKICAgICAgICAgICByZGY6cmVzb3VyY2U9Imh0dHA6Ly9wdXJsLm9yZy9kYy9kY21pdHlwZS9TdGlsbEltYWdlIiAvPgogICAgICAgIDxkYzp0aXRsZSAvPgogICAgICA8L2NjOldvcms+CiAgICA8L3JkZjpSREY+CiAgPC9tZXRhZGF0YT4KICA8ZwogICAgIGlua3NjYXBlOmxhYmVsPSJMYXllciAxIgogICAgIGlua3NjYXBlOmdyb3VwbW9kZT0ibGF5ZXIiCiAgICAgaWQ9ImxheWVyMSIKICAgICB0cmFuc2Zvcm09InRyYW5zbGF0ZSgwLC04NTIuMzYyMTYpIj4KICAgIDxwYXRoCiAgICAgICBzdHlsZT0iZmlsbDojMDAwMDAwIgogICAgICAgZD0iIgogICAgICAgaWQ9InBhdGg0OTIzIgogICAgICAgaW5rc2NhcGU6Y29ubmVjdG9yLWN1cnZhdHVyZT0iMCIgLz4KICAgIDxwYXRoCiAgICAgICBzdHlsZT0iZmlsbDp1cmwoI2xpbmVhckdyYWRpZW50NDkzMyk7ZmlsbC1vcGFjaXR5OjE7ZmlsbC1ydWxlOmV2ZW5vZGQ7c3Ryb2tlOiMwMDAwMDA7c3Ryb2tlLXdpZHRoOjFweDtzdHJva2UtbGluZWNhcDpidXR0O3N0cm9rZS1saW5lam9pbjptaXRlcjtzdHJva2Utb3BhY2l0eToxIgogICAgICAgZD0ibSA0NS40NTY4NjUsMTAxMi4yNzg0IDExNC4xNDcyMzUsLTAuNTA1MSBjIDQyLjE5NDU4LC0wLjE4NjcgMjUuNjIxODIsLTgyLjA5MzkyIC0xNy4xNzI1OSwtNjUuNjU5OSAwLDAgLTUuNTY0MjQsLTI2LjI4NjU4IC0zMy4zMzUwNCwtMTYuMTYyNDQgLTUuMjk5OTQsLTM0LjU1NTgyIC02Ny42MTI5ODMsLTMyLjUyNzE1IC02Ni42NzAwNjMsMTQuMTQyMTMgLTQ1LjEyNDI0NzUsMzguNDMzNzYgLTExLjIyMzcwMiw2OC4yNDg0MSAzLjAzMDQ1OCw2OC4xODUzMSB6IgogICAgICAgaWQ9InBhdGg0OTI1IgogICAgICAgaW5rc2NhcGU6Y29ubmVjdG9yLWN1cnZhdHVyZT0iMCIKICAgICAgIHNvZGlwb2RpOm5vZGV0eXBlcz0ic3NjY2NzIiAvPgogIDwvZz4KPC9zdmc+Cg==" alt="">
                            <h6 class="file-name pointfile-name my-2"></h6>
                            <div class="d-flex position-relative my-2 w-50">
                                <div class="progress progress-point w-100" style="height: 1rem;" v-if="progressUploadPoint >= 0">
                                    <div class="progress-bar progress-bar-striped bg-primary" :style="'width: ' + progressUploadPoint + '%'">
                                        <span>{{ progressUploadPoint }}% Complete</span>
                                    </div>
                                </div>
                                <i class="icon-checkmark2 text-primary position-absolute" style="right: -20px" v-if="progressUploadPoint >= 100"></i>
                            </div>
                            <!-- <h6 class="my-3 error-message text-danger" v-if="progressUploadPoint < 0">
                                Something went wrong, <a href="#" @click="reupPointFile">Re-upload</a>
                            </h6> -->
                            <h5 class="delete-file cursor-pointer text-danger" v-if="progressUploadPoint < 0 || progressUploadPoint >= 100" @click="removePoint">
                                <i class="icon-cancel-circle2 mr-2"></i>Delete file
                            </h5>
                        </div>
                        <input type="hidden" name="PointCloud[point_file]" v-model="pointFile">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <h4 class="font-weight-bold text-center mt-3 mb-4">Point cloud parameters</h4>
                </div>
                <!-- <div class="col-md-6 form-group">
                    <label for="spacing">Spacing</label>
                    <input type="text" name="Params[spacing]" class="form-control">
                </div>
                <div class="col-md-6 form-group">
                    <label for="spacing-by-diagonal-fraction">Spacing By Diagonal Fraction</label>
                    <input type="text" name="Params[spacing-by-diagonal-fraction]" class="form-control">
                </div>
                <div class="col-md-6 form-group">
                    <label for="levels">Levels</label>
                    <input type="text" name="Params[levels]" class="form-control">
                </div>
                <div class="col-md-6 form-group">
                    <label for="input-format">Input Format</label>
                    <input type="text" name="Params[input-format]" class="form-control">
                </div>
                <div class="col-md-6 form-group">
                    <label for="source-listing-only">Source Listing Only</label>
                    <input type="text" name="Params[source-listing-only]" class="form-control">
                </div> -->
                <div class="col-md-6 form-group">
                    <label for="edl-enabled" class="d-block">Edl Enabled</label>
                    <radio-button :selected="edlEnabled" :value="1" :label="'True'" @choose="edlEnabled = $event"></radio-button>
                    <radio-button :selected="edlEnabled" :value="0" :label="'False'" @choose="edlEnabled = $event"></radio-button>
                    <input type="hidden" name="Params[edl-enabled]" v-model="edlEnabled">
                </div>
                <div class="col-md-6 form-group">
                    <label for="output-format" class="d-block">Output Format</label>
                    <radio-button :selected="outputFormat" :value="'BINARY'" :label="'BINARY'" @choose="outputFormat = $event"></radio-button>
                    <radio-button :selected="outputFormat" :value="'LAS'" :label="'LAS'" @choose="outputFormat = $event"></radio-button>
                    <radio-button :selected="outputFormat" :value="'LAZ'" :label="'LAZ'" @choose="outputFormat = $event"></radio-button>
                    <input type="hidden" name="Params[output-format]" v-model="outputFormat">
                </div>
                <!-- <div class="col-md-12 form-group">
                    <label for="aabb">AABB</label>
                    <div class="row">
                        <div class="col-md-2">
                            <input type="text" v-model="aabb.minX" class="form-control" placeholder="minX">
                        </div>
                        <div class="col-md-2">
                            <input type="text" v-model="aabb.minY" class="form-control" placeholder="minY">
                        </div>
                        <div class="col-md-2">
                            <input type="text" v-model="aabb.minZ" class="form-control" placeholder="minZ">
                        </div>
                        <div class="col-md-2">
                            <input type="text" v-model="aabb.maxX" class="form-control" placeholder="maxX">
                        </div>
                        <div class="col-md-2">
                            <input type="text" v-model="aabb.maxY" class="form-control" placeholder="maxY">
                        </div>
                        <div class="col-md-2">
                            <input type="text" v-model="aabb.maxZ" class="form-control" placeholder="maxZ">
                        </div>
                    </div>
                    <input type="hidden" name="Params[aabb]" v-model="aabbString">
                </div>
                <div class="col-md-6 form-group">
                    <label for="scale">Scale</label>
                    <div class="row">
                        <div class="col-md-4">
                            <input type="text" v-model="scale.X" class="form-control" placeholder="X">
                        </div>
                        <div class="col-md-4">
                            <input type="text" v-model="scale.Y" class="form-control" placeholder="Y">
                        </div>
                        <div class="col-md-4">
                            <input type="text" v-model="scale.Z" class="form-control" placeholder="Z">
                        </div>
                    </div>
                    <input type="hidden" name="Params[scale]" v-model="scaleString">
                </div> -->
                <div class="col-md-6 form-group">
                    <label for="marterial" class="d-block">Point Color Type</label>
                    <checkbox-button :selected="marterial" :value="'RGB'" :label="'RGB'"></checkbox-button>
                    <checkbox-button :selected="marterial" :value="'ELEVATION'" :label="'ELEVATION'"></checkbox-button>
                    <checkbox-button :selected="marterial" :value="'INTENSITY'" :label="'INTENSITY'"></checkbox-button>
                    <checkbox-button :selected="marterial" :value="'INTENSITY_GRADIENT'" :label="'INTENSITY_GRADIENT'"></checkbox-button>
                    <checkbox-button :selected="marterial" :value="'RETURN_NUMBER'" :label="'RETURN_NUMBER'"></checkbox-button>
                    <checkbox-button :selected="marterial" :value="'SOURCE'" :label="'SOURCE'"></checkbox-button>
                    <checkbox-button :selected="marterial" :value="'LEVEL_OF_DETAIL'" :label="'LEVEL_OF_DETAIL'"></checkbox-button>
                    <input type="hidden" name="Params[marterial]" v-model="marterialString">
                </div>
                <div class="col-md-6 form-group">
                    <label for="projection">Projection</label>
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <vue-bootstrap-typeahead placeholder="Search EPSG Codes" @hit="generateProj4Code" v-model="epsgSelected" :data="epsgCodes"/>
                        </div>
                        <div class="col-md-12">
                            <textarea id="proj4-code" rows="4" name="Params[projection]" placeholder="Proj4" class="form-control"></textarea>
                        </div>
                    </div>
                </div>
                <!-- <div class="col-md-6 form-group">
                    <label for="color-range" class="d-block">Color Range</label>
                    <radio-button :selected="colorRange" :value="1" :label="'True'" @choose="colorRange = $event"></radio-button>
                    <radio-button :selected="colorRange" :value="0" :label="'False'" @choose="colorRange = $event"></radio-button>
                    <input type="hidden" name="Params[color-range]" v-model="colorRange">
                </div> -->
                <!-- <div class="col-md-6 form-group">
                    <label for="intensity-range" class="d-block">Intensity Range</label>
                    <radio-button :selected="intensityRange" :value="1" :label="'True'" @choose="intensityRange = $event"></radio-button>
                    <radio-button :selected="intensityRange" :value="0" :label="'False'" @choose="intensityRange = $event"></radio-button>
                    <input type="hidden" name="Params[intensity-range]" v-model="intensityRange">
                </div> -->
                <!-- <div class="col-md-6 form-group">
                    <label for="show-skybox" class="d-block">Show Skybox</label>
                    <radio-button :selected="showSkybox" :value="1" :label="'True'" @choose="showSkybox = $event"></radio-button>
                    <radio-button :selected="showSkybox" :value="0" :label="'False'" @choose="showSkybox = $event"></radio-button>
                    <input type="hidden" name="Params[show-skybox]" v-model="showSkybox">
                </div> -->
                <!-- <div class="col-md-6 form-group">
                    <label for="output-attributes" class="d-block">Output Attributes</label>
                    <checkbox-button :selected="outputAttributes" :value="'RGB'" :label="'RGB'"></checkbox-button>
                    <checkbox-button :selected="outputAttributes" :value="'INTENSITY'" :label="'INTENSITY'"></checkbox-button>
                    <checkbox-button :selected="outputAttributes" :value="'CLASSIFICATION'" :label="'CLASSIFICATION'"></checkbox-button>
                    <input type="hidden" name="Params[output-attributes]" v-model="outputAttributesString">
                </div> -->
                <div class="col-md-6 form-group">
                    <input type="hidden" name="Params[title]" v-model="title">
                    <input type="hidden" name="Params[description]" v-model="description">
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <h4 class="font-weight-bold text-center mt-3 mb-4">Point cloud location</h4>
                </div>
                <div class="col-md-12 form-group">
                    <div class="map-widget">
                        <map-picker :latitude="null" :longitude="null"></map-picker>
                    </div>
                </div>
            </div>
            <div class="row">
                <!-- <div class="col-md-12 form-group text-center">
                    <button type="submit" class="btn btn-primary btn-lg btn-upload-pointcloud text-uppercase" 
                        :disabled="!thumbnail || !pointFile || !title" @click="uploadPointCloud">
                        <i class="icon-cloud-upload icon-2x mr-2"></i>Upload point cloud
                    </button>
                </div> -->
                <div class="col-md-12 form-group text-center">
                    <button type="submit" class="btn btn-primary btn-lg btn-upload-pointcloud text-uppercase" 
                        :disabled="!thumbnail || !title" @click="uploadPointCloud">
                        <i class="icon-cloud-upload icon-2x mr-2"></i>Upload point cloud
                    </button>
                </div>
            </div>
        </form>
    </div>
    <alert-modal :message="uploadSuccess.message" :url="uploadSuccess.url" :key="uploadSuccess.url + uploadSuccess.message"></alert-modal>
</div>

<script>
    $(function() {
        Vue.component('vue-bootstrap-typeahead', VueBootstrapTypeahead);
        var vm = new Vue({
            el: '#pointcloud-upload-page',
            data: {
                thumbnail: null,
                pointFile: null,
                chooseThumbnail: false,
                choosePointFile: false,
                progressUploadThumbnail: 0,
                progressUploadPoint: 0,
                title: '',
                description: '',
                edlEnabled: 1,
                colorRange: 1,
                intensityRange: 1,
                outputFormat: 'LAZ',
                outputAttributes: ['RGB', 'INTENSITY'],
                scale: {
                    X: '',
                    Y: '',
                    Z: ''
                },
                aabb: {
                    minX: '',
                    minY: '',
                    minZ: '',
                    maxX: '',
                    maxY: '',
                    maxZ: ''
                },
                showSkybox: 1,
                marterial: ['ELEVATION'],
                uploadSuccess: {
                    message: '',
                    url: '#'
                },
                epsgCodes: [],
                epsgSelected: null
            },
            computed: {
                outputAttributesString: function() {
                    return $.trim(this.outputAttributes.join(" "));
                },
                scaleString: function() {
                    return $.trim(Object.values(this.scale).join(" "));
                },
                aabbString: function() {
                    return $.trim(Object.values(this.aabb).join(" "));
                },
                marterialString: function() {
                    return $.trim(this.marterial.join(" "));
                }
            },
            created: function() {
                this.getEPSGCodes();
            },
            methods: {
                getEPSGCodes: function() {
                    var _this = this,
                        api = '<?= APPConfig::getUrl('point-cloud/get-epsg-codes') ?>';

                    sendAjax(api, {}, function(resp) {
                        _this.epsgCodes = resp;
                    }, 'GET');
                },

                generateProj4Code: function() {
                    var _this = this,
                        api = '<?= APPConfig::getUrl('point-cloud/generate-proj4-code') ?>',
                        data = { epsg: _this.epsgSelected };
                    sendAjax(api, data, function(resp) {
                        if(resp.status) {
                            $('#proj4-code').empty().append(resp.code);
                        } else {
                            $('#proj4-code').empty().append('#error');
                        }
                    })
                },

                readFileInfo: function(e) {
                    var _this = this,
                        input = e.target,
                        api = '<?= CMSConfig::getUrl('file/upload') ?>'

                    this.uploadFiles(input.files, api, (resp) => {
                        if (resp.fails.length > 0) {
                            toastMessage('error', resp.fails[0] + ' cannot be uploaded')
                        }
                        if (resp.successes.length > 0) {
                            _this.thumbnail = resp.successes[0].path
                        }
                    })
                },

                readPointInfo: function(e) {
                    var _this = this,
                        input = e.target,
                        api = '<?= CMSConfig::getUrl('file/upload-point') ?>'

                    this.uploadPointFile(input.files, api, (resp) => {
                        if (resp.fails.length > 0) {
                            toastMessage('error', resp.fails[0] + ' cannot be uploaded')
                        }
                        if (resp.successes.length > 0) {
                            _this.pointFile = resp.successes[0].path
                        }
                    })
                },

                uploadFiles: function(files, api, callback) {
                    var _this = this;
                    var form = new FormData();
                    var file = files[0];

                    if (['image/jpeg', 'image/jpg', 'image/png'].indexOf(file.type) == -1) {
                        toastMessage('error', file.name + ': Unsupported file format: jpg/jpeg')
                    } else if (file.size > 5242880) {
                        toastMessage('error', file.name + ': Maximum size exceeded: 5MB')
                    } else {
                        form.append('Files[]', file, file.name);
                        _this.chooseThumbnail = true
                        _this.$nextTick(function() {
                            var reader = new FileReader();
                            reader.onload = function (e) {
                                $('.thumbnail').attr('src', e.target.result)
                                $('.thumbnail-name').empty().append(file.name)
                            }
                            reader.readAsDataURL(file);
                        })
                    }

                    if(form.has('Files[]')) {
                        var xhr = new XMLHttpRequest();
                        xhr.upload.addEventListener('progress', function(evt){
                            if (evt.lengthComputable) {
                                var percent = (evt.loaded / evt.total) * 100
                                _this.progressUploadThumbnail = Math.round(percent)
                            }
                        }, false)
                        xhr.addEventListener('load', function(evt) {
                            if(this.status == 200) {
                                var resp = JSON.parse(this.response)
                                callback(resp)
                            }
                        }, false);
                        xhr.addEventListener('error', function(evt) {
                            _this.progressUploadThumbnail = -1
                        }, false);

                        xhr.open('POST', api);
                        xhr.send(form);
                    }
                },

                uploadPointFile: function(files, api, callback) {
                    var _this = this
                    var form = new FormData()
                    
                    for(let i = 0; i < files.length; i++) {
                        var file = files[i];

                        if (!this.checkPointFileFormat(file.name)) {
                            toastMessage('error', file.name + ': Unsupported file format: las/laz/ptx/ply/rar/zip')
                        } else if (file.size > 1073741824) {
                            toastMessage('error', file.name + ': Maximum size exceeded: 1G')
                        } else {
                            form.append('Points[]', file, file.name);
                            _this.choosePointFile = true;
                            _this.$nextTick(function() {
                                $('.pointfile-name').empty().append(file.name)
                            })
                        }
                    }
                        
                    if(form.has('Points[]')) {
                        var xhr = new XMLHttpRequest();
                        xhr.upload.addEventListener('progress', function(evt){
                            if (evt.lengthComputable) {
                                var percent = (evt.loaded / evt.total) * 100
                                _this.progressUploadPoint = Math.round(percent)
                            }
                        }, false)
                        xhr.addEventListener('load', function(evt) {
                            if(this.status == 200) {
                                var resp = JSON.parse(this.response)
                                callback(resp)
                            }
                        }, false);
                        xhr.addEventListener('error', function(evt) {
                            _this.progressUploadPoint = -1
                        }, false);

                        xhr.open('POST', api);
                        xhr.send(form);
                    }
                },

                removeThumbnail: function() {
                    this.deleteFile(this.thumbnail)
                    this.thumbnail = null
                    this.chooseThumbnail = false
                    this.progressUploadThumbnail = 0
                },

                removePoint: function() {
                    this.deleteFile(this.pointFile)
                    this.pointFile = null
                    this.choosePointFile = false
                    this.progressUploadPoint = 0
                },

                deleteFile: function(file) {
                    var _this = this
                    var api = '<?= CMSConfig::getUrl('file/delete') ?>',
                        data = {
                            file: file
                        }
                    sendAjax(api, data, function(resp) {})
                },

                checkPointFileFormat: function(filename) {
                    var ext = filename.split('.').pop();
                    if(['las', 'laz', 'ptx', 'ply', 'rar', 'zip'].includes(ext)) return true

                    return false
                },

                uploadPointCloud: function(e) {
                    e.preventDefault()
                    var _this = this,
                        api = '<?= APPConfig::getUrl('point-cloud/upload') ?>',
                        form = $('#pointcloud-upload-form'),
                        data = form.serialize(),
                        ladda = Ladda.create($(".btn-upload-pointcloud")[0])
                    ladda.start()
                    sendAjax(api, data, function(resp) {
                        if (resp.status) {
                            _this.uploadSuccess.message = resp.message
                            _this.uploadSuccess.url = '<?= Yii::$app->homeUrl ?>'
                            _this.$nextTick(function() {
                                $('#alert-modal').modal({backdrop: 'static', keyboard: false});
                            })
                        } else {
                            toastMessage('error', resp.message)
                        }
                        ladda.stop()
                    })
                }
            }
        })
    })
</script>