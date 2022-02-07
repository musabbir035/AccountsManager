<template>
  <ul class="sidebar-nav">
    <li v-for="item in items" :key="item.title" class="nav-item">
      <div v-if="item.subLinks">
        <a href="javascript:void(0)" class="nav-link" @click="toggleSubmenu">
          <i :class="item.icon"></i>
          <p>
            {{ item.title }}
            <i class="fas fa-chevron-down"></i>
          </p>
        </a>
        <ul class="sidebar-submenu">
          <li
            v-for="subLink in item.subLinks"
            :key="subLink.title"
            class="nav-item"
          >
            <router-link
              :to="subLink.routeName ? { name: subLink.routeName } : '#'"
              class="nav-link"
            >
              <i :class="subLink.icon"></i>
              <p>{{ subLink.title }}</p>
            </router-link>
          </li>
        </ul>
      </div>
      <div v-else>
        <router-link
          :to="item.routeName ? { name: item.routeName } : '#'"
          class="nav-link"
          :exact="item.exact"
        >
          <i :class="item.icon"></i>
          <p>{{ item.title }}</p>
        </router-link>
      </div>
    </li>
  </ul>
</template>

<script>
export default {
  props: ["items"],
  mounted() {
    const toggleSubmenu = function (e) {
      if (e.target.classList.contains("submenu-open")) {
        e.target.classList.remove("submenu-open");
      } else {
        e.target.classList.add("submenu-open");
      }
    };

    return { toggleSubmenu };
  },
};
</script>
