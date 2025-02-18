<script setup>
import WelcomeItem from './WelcomeItem.vue'
import RideSharingIcon from './icons/IconDocumentation.vue'
import SafetyIcon from './icons/IconDocumentation.vue'
import CommunityIcon from './icons/IconCommunity.vue'
import SupportIcon from './icons/IconSupport.vue'
</script>

<template>
  <WelcomeItem>
    <template #icon>
      <RideSharingIcon />
    </template>
    <template #heading>Ride Sharing</template>

    Our ride sharing service connects drivers with riders, making transportation more accessible and convenient.
  </WelcomeItem>

  <WelcomeItem>
    <template #icon>
      <SafetyIcon />
    </template>
    <template #heading>Safety</template>

    Safety is our top priority. We ensure all drivers are vetted and vehicles are regularly inspected to ensure a safe ride for all.
  </WelcomeItem>

  <WelcomeItem>
    <template #icon>
      <CommunityIcon />
    </template>
    <template #heading>Community</template>

    Join our community of drivers and riders to make a positive impact on your city's transportation.
  </WelcomeItem>

  <WelcomeItem>
    <template #icon>
      <SupportIcon />
    </template>
    <template #heading>Support</template>

    If you have any questions or concerns, our support team is here to help. Contact us anytime for assistance.
  </WelcomeItem>
</template>
