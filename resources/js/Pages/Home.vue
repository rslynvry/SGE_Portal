<template>
    <title>Home - COMELEC Portal</title>
    <Navbar></Navbar>

    <main>
        <div class="banner">
            <img src="../../images/Home/banner.jpg" alt="" class="banner-img">
        </div>

        <div class="features">
            <div class="features-wrapper">
                <a href="/elections" class="select-feature">
                    <div class="feature-information">
                        <img src="../../images/Home/student.png" alt="" class="feature-img">
                        <span class="feature-title">FILE FOR CANDIDACY</span>
                        <span class="feature-description">Step up, be the leader your school deserves.</span>
                    </div>
                </a>
                
                <a href="/elections" class="select-feature">
                    <div class="feature-information">
                        <img src="../../images/Home/team.png" alt="" class="feature-img">
                        <span class="feature-title">REGISTER YOUR PARTY</span>
                        <span class="feature-description">Create your crew, let your collective voices echo.</span>
                    </div>
                </a>
    
                <div class="select-feature" @click="openModal" style="cursor: pointer;">
                    <div class="feature-information">
                        <img src="../../images/Home/talk.png" alt="" class="feature-img">
                        <span class="feature-title">FILE AN APPEAL</span>
                        <span class="feature-description">Stand tall, question the status quo for what’s right.</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="appeal-modal" v-show="isModalOpen" @click="outsideClick">
            <div class="appeal-content">
                <div class="appeal-header">
                    <h1 class="header-label">File an Appeal</h1>
                </div>
                
                <label class="form-label" for="name">Student Number <ToolTip class="mx-2"><slot>Linked email of this student number will be used when appeal is submitted.</slot></ToolTip></label>
                <input v-model="student_number" :disabled="submitting" class="form-control margin" style="border: 1px solid #c2c2c2;" type="text" name="name" maxlength="15">

                <label for="desc" class="form-label">Appeal details</label>
                <textarea v-model="appeal_details" :disabled="submitting" class="form-control margin details" type="text" name="desc"></textarea>

                <label class="form-label">Upload Attachment <span style="font-family: Arial, Helvetica, sans-serif; font-size: 14px;">(Optional. 3mb max)</span> <ToolTip class="mx-2"><slot>If you have multiple images to provide, please compile it in pdf.</slot></ToolTip></label>
                <input ref="attachmentLabel" @change="updateAttachment" :disabled="submitting" type="file" accept="image/*,.pdf" style="border: 1px solid #c2c2c2;" name="attachment" class="form-control">

                <div class="appeal-buttons">
                    <button class="cancel-button" :disabled="submitting" @click.prevent="closeModal">Cancel</button>
                    <ActionButton class="submit-button" :disabled="submitting" @click.prevent="submit">{{ submitButtonText }}</ActionButton>
                </div>
            </div>
        </div>

        <hr class="line">
        
        <div class="header">
            <div>
                <span class="header-title">RECENT ANNOUNCEMENTS</span>
            </div>
            <div>
                <button class="view-button" @click.prevent="redirectToAllAnnouncements">View All</button>
            </div>
        </div>
        
        <ImageSkeleton v-if="isRecentLoading" 
                            :loading="isRecentLoading" 
                            :itemCount="3" 
                            :borderRadius="'10px'"
                            :imageWidth="'460px'" 
                            :imageHeight="'300px'"
                            :containerMargin="'0% 10%'"
                            :itemMargin="'.5%'">
        </ImageSkeleton>
        <div v-else class="announcements">
            <div class="announcements-wrapper">
                <div v-for="(recent, index) in recentData" :key="index" class="select-announcement" style="cursor: pointer;" @click.prevent="displaySelectedRecentAnnouncement(recent.id)">
                    <div class="announcement-information">
                        <img class="announcement-img" v-if="recent.images.length > 0" :src="recent.images[0].url" alt="">
                        <img class="announcement-img" v-else src="" alt="?">
                        <span class="announcement-title ellipsis">{{ recent.title }}</span>
                        <span class="announcement-date">{{ formatDate(recent.created_at) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <hr class="line">

        <div class="redirect">
            <div class="redirect-wrapper">
                <div class="information">
                    <img src="../../images/Home/votemsg.jpg" alt="" class="vote-img">
                    <div class="message">
                        <span class="message-title">📣 Your Voice Matters! Make It Heard!</span>
                        <p class="message-body">Let’s make our voices heard and our votes count. Because together, we can make a difference. 
                            Together, we can shape the future.
                            <br><br>
                            Remember: <strong>Your Vote, Your Voice, Your Future!</strong>
                            </p>
                        <a href="https://sge-voting-v93v4.ondigitalocean.app">
                            <button class="ems-button">Vote Now!</button>
                        </a>
                    </div>
                    <img src="../../images/Home/votemsg2.jpg" alt="" class="vote-img">
                </div>
            </div>
        </div>

        <hr class="line">

        <div class="redirect mb-5">
            <div class="redirect-wrapper">
                <div class="information">
                    <img src="../../images/Home/manage.jpg" alt="" class="manage-img">
                    <div class="message">
                        <span class="message-title">Administer Student Elections Here!</span>
                        <p class="message-body">This is your hub for overseeing and managing the student elections. 
                            Remember, a well-managed election is the foundation of a successful student council.</p>
                        <a href="https://sge-ems-6uaae.ondigitalocean.app">
                            <button class="ems-button">Go to EMS</button>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </main>
</template>

<script>
    import Standards from '../Shared/Standards.vue'
    import Navbar from '../Shared/Navbar.vue'
    import Carousel from '../Shared/Carousel.vue'
    import ImageSkeleton from '../Skeletons/ImageSkeleton.vue'
    import Appeal from '../Shared/Appeal.vue'
    import ActionButton from '../Shared/ActionButton.vue'
    import ToolTip from '../Shared/Tooltip.vue'
    import { Link, router } from '@inertiajs/vue3'

    import { ref } from 'vue'
    import { useQuery } from "@tanstack/vue-query";
    import { useAnnouncementStore } from '../Stores/AnnouncementStore'

    import axios from 'axios'

    export default {
        setup() {
            const isModalOpen = ref(false)
            const student_number = ref('')
            const appeal_details = ref('')
            const attachment = ref(null)
            const submitting = ref(false)

            const images = ref(['../../images/Home/puplogo.png'])
            const recent_announcements = ref([])

            const store = useAnnouncementStore()

            const fetchRecentAnnouncements = async () => {
                const response = await axios.get(`${import.meta.env.VITE_FASTAPI_BASE_URL}/api/v1/announcement/all`, {
                    params: {
                        include_images: true
                    }
                })

                console.log(`Get all recent announcements successful. Duration: ${response.duration}ms`)

                const announcements = response.data.announcements.map(announcement => ({
                    id: announcement.AnnouncementId,
                    title: announcement.AnnouncementTitle,
                    body: announcement.AnnouncementBody,
                    announcement_type: announcement.AnnouncementType,
                    attachment_type: announcement.AttachmentType,
                    images: announcement.images,
                    created_at: announcement.created_at,
                }))

                return announcements.slice(0, 3)
            }

            const { data: recentData, 
                    isLoading: isRecentLoading, 
                    isSuccess: isRecentSucess, 
                    isError: isRecentError, } = 
                    useQuery({
                        queryKey: ['recentAnnouncements'],
                        queryFn: fetchRecentAnnouncements,
                    })

            return {
                isModalOpen,
                student_number,
                appeal_details,
                attachment,
                submitting,

                images,
                recent_announcements,
                store,

                recentData,
                isRecentLoading,
                isRecentSucess,
                isRecentError,
            }
        },
        components: {
            Standards,
            Navbar,
            Carousel,
            Link,
            ImageSkeleton,
            Appeal,
            ActionButton,
            ToolTip,
        },
        computed:{
            submitButtonText(){
                return this.submitting ? 'Submitting..' : 'Submit';
            }
        },
        methods: {
            openModal(){
                this.isModalOpen = true;
            },
            closeModal(){
                this.isModalOpen = false;
            },
            outsideClick(event){
                if (event.target.className === 'appeal-modal') {
                    this.closeModal();
                }
            },
            clearInputs(){
                this.student_number = '';
                this.appeal_details = '';
                
                this.attachment = null;
                this.$refs.attachmentLabel.value = '';
            },
            updateAttachment(event) {
                const file = event.target.files[0];
                if (file.size > 1024 * 1024 * 3) {
                    alert('File size exceeds 3mb.');
                    return;
                }
                const reader = new FileReader();
                reader.onload = e => {
                    this.attachment = e.target.result;
                };
                reader.readAsDataURL(file);
            },
            verifyInputs() {
                if (this.student_number === '') {
                    alert('Please enter your student number.');
                    return false;
                }
                if (this.appeal_details === '') {
                    alert('Please enter your appeal details.');
                    return false;
                }
                return true;
            },
            submit() {
                if (this.verifyInputs()) {
                    const data = {
                        student_number: this.student_number,
                        appeal_details: this.appeal_details,
                        attachment: ''
                    }

                    if (this.submitting) {
                        return;
                    }

                    this.submitting = true;
                    
                    if (this.attachment) {
                        data.attachment = this.attachment;
                    }
                    
                    axios.post(`${import.meta.env.VITE_FASTAPI_BASE_URL}/api/v1/election-appeals/submit`, data)
                    .then(response => {
                        alert('Appeal submitted successfully.');
                        this.closeModal();
                        this.clearInputs();
                    })
                    .catch(error => {
                        console.log(error);
                        if (error.response.data.error) {
                            alert(error.response.data.error);
                        }
                        else {
                            alert('An error occured while submitting your appeal.');
                        }
                    })
                    .finally(() => {
                        this.submitting = false;
                    })
                }
            },
            redirectToAllAnnouncements() {
                router.visit('/announcements');
            },
            displaySelectedRecentAnnouncement(id) {
                router.visit(`/announcements/view?id=${id}`);
            },
            redirectToElection() {
                router.visit('/elections');
            },
            formatDate(date) {
                return new Date(date).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' });
            },
            appeal() {
                this.$modal.show(Appeal)
            },
            goToVoting() {
                window.location.href = 'https://sge-voting-byvcc.ondigitalocean.app'
            },
            goToEMS() {
                window.location.href = 'https://king-prawn-app-2ep7s.ondigitalocean.app'
            }
        }
    }

</script>

<style scoped>
    .banner{
        height: 600px;
    }

    .banner-img{
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .features{
        margin: 2.5% 0%;
    }

    .features-wrapper{
        margin: 0% 8%;
        display: flex;
        flex-wrap: wrap;
        justify-content: space-around;
    }

    .select-feature{
        text-decoration: none;
        color: black;
    }

    .select-feature:hover{
        color: black;
        .feature-information{
            transform: translateY(-10px);
        }
    }

    .feature-information{
        background-color: white;
        display: flex;
        justify-content: center;
        align-items: center;
        flex-direction: column;
        width: 300px;
        height: 300px;
        border-radius: 5px;
        box-shadow: 0px 3px 5px rgba(167, 165, 165, 0.5);
        transition: transform 0.4s ease;
    }

    .feature-img{
        height: 150px;
    }

    .feature-title{
        font-weight: bold;
        font-size: 22px;
        margin-top: 20px;
    }

    .feature-description{
        text-align: center;
        font-size: 18px;
        margin: 0px 30px;
    }

    .header{
        margin-top: 2.5% !important;
        margin: 0% 8%;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .header-title{
        font-size: 28px;
        font-weight: bold;
    }

    .view-button{
        border: transparent;
        background-color: transparent;
        font-size: 20px;
    }

    .view-button:hover{
        color: #800000;
    }

    .announcements{
        margin-top: 0.5%;
        margin-bottom: 1.5%;
    }

    .announcements-wrapper{
        margin: 0% 8%;
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
    }

    .select-announcement{
        text-decoration: none;
        color: black;
        margin: 1% 0.98%;
    }

    .select-announcement:hover{
        color: black;
        .announcement-information{
            transform: scale(1.009);
        }
    }

    .announcement-information{
        display: flex;
        justify-content: center;
        align-items: start;
        flex-direction: column;
        box-shadow: 0px 3px 5px rgba(167, 165, 165, 0.5);
        background-color: white;
        transition: transform 0.4s ease;
        width: 500px;
    }

    .announcement-img{
        width: 500px;
        height: 273px;
        object-fit: cover;
    }

    .announcement-title{
        font-weight: bold;
        font-size: 18px;
        margin: 0px 5px;
        width: 100%;
    }

    .announcement-date{
        font-size: 18px;
        margin: 0px 5px
    }

    .line{
        border: 0;
        height: 2px;
        background: rgb(230,233,237);
        background: linear-gradient(90deg, rgba(230,233,237,1) 0%, rgba(166,176,190,1) 50%, rgba(230,233,237,1) 100%);
        margin: 2% 0%;
    }

    .redirect{
        margin-top: 0.5%;
        
    }

    .redirect-wrapper{
        margin: 0% 8%;
        display: flex;
        flex-wrap: wrap;
    }

    .information{
        background-color: #ffffff;
        box-shadow: 0px 3px 5px rgba(167, 165, 165, 0.5);
        padding: 1.5%;
        width: 100%;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .manage-img{
        height: 360px;
        width: 800px;
        object-fit: cover;
    }

    .message{
        margin-left: 2%;
    }

    .message-title{
        font-size: 25px;
        font-weight: bold;
    }

    .message-body{
        font-size: 18px;
        margin-top: 1%;
    }

    .ems-button{
        width: 100%;
        border: transparent;
        border-radius: 25px;
        padding: 1% 0%;
        font-size: 18px;
        color: white;
        background-color: #800000;
    }

    .vote-img{
        height: 360px;
    }

    .appeal-modal{
        display: block;
        position: fixed;
        z-index: 99;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0,0,0,0.4);
        height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .appeal-content{
        position: fixed;
        z-index: 98;
        width: 500px; /* Adjust as needed */
        height: auto; /* Adjust as needed */
        background-color: rgb(255, 255, 255);
        font-family: 'Inter', sans-serif;
        padding: 1.5%;
        border-radius: 5px;
        height: min-content;
    }

    .header-label{
        font-size: 30px;
        color: #800000;
        margin-bottom: 5%;
        text-align: center;
        font-weight: 600;
    }

    .margin{
        margin-bottom: 4%;
    }

    .details{
        background-color: white;
        border: 1px solid #c2c2c2;
        resize: none;
        overflow-y: auto;
        height: 150px;
    }

    .appeal-buttons{
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-top: 6%;
    }

    .cancel-button{
        padding-top: 20px;
        padding-bottom: 20px;
        padding: 15px 20px 15px 20px;
        border: transparent;
        border-radius: 10px;
        background-color: transparent;
        color: #CC3300;
        width: fit-content;
    }

    .submit-button{
        font-family: 'Inter', sans-serif !important;
    }

    .appeal-button {
        position: fixed;
        right: 20px;
        bottom: 20px;
        padding: 0%;
        width: 52px !important;
        height: 50px !important;
        z-index: 98;
        border-radius: 50px !important
    }

    .ellipsis {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
</style>