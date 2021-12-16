              <div class="aside-menu-wrapper flex-column-fluid" id="kt_aside_menu_wrapper">
                  <!--begin::Menu Container-->
                  <div id="kt_aside_menu" class="aside-menu my-4" data-menu-vertical="1" data-menu-scroll="1" data-menu-dropdown-timeout="500">
                      <ul class="menu-nav">

                          <?php

                            $user_name = $this->session->userdata('user_id');

                            $SideMenu_records = Fetch_Users_Access_Control_Menu($user_name['id']);
                            if ($SideMenu_records != NULL) {
                                foreach ($SideMenu_records as  $obj_SideMenu_records) {
                                    if ($obj_SideMenu_records['parent_id'] == '26') { ?>
                                      <li class="menu-item" aria-haspopup="true">
                                          <a href="<?= base_url('dashboard') ?>" class="menu-link">
                                              <i class="<?= $obj_SideMenu_records['icon'] ?> mr-4 ml-2 mt-2"></i>
                                              <span class="menu-text"><?= $obj_SideMenu_records['name'] ?></span>
                                          </a>
                                      </li>

                                  <?php } else {

                                    ?>
                                      <li class="menu-item" id='menu_id_<?= $obj_SideMenu_records['parent_id'] ?>' aria-haspopup="true" data-menu-toggle="hover">
                                          <a href="javascript:;" class="menu-link menu-toggle">
                                              <i class="fa <?= $obj_SideMenu_records['icon'] ?> mr-4 ml-2 mt-2"></i>
                                              <!-- </span> -->
                                              <span class="menu-text"><?= $obj_SideMenu_records['name'] ?></span>
                                              <i class="menu-arrow"></i>
                                          </a>
                                          <div class="menu-submenu">
                                              <i class="menu-arrow"></i>
                                              <ul class="menu-subnav">
                                                  <?php
                                                    // echo json_encode($obj_SideMenu_records);
                                                    // die();
                                                    foreach ($obj_SideMenu_records['children'] as $child) {
                                                    ?>
                                                      <li class="menu-item" id='submenu_id_<?= $child['page_id'] ?>' aria-haspopup="true">
                                                          <a href="<?= base_url() . $obj_SideMenu_records['link'] . '/'   . $child['sub_link'] ?>" class="menu-link">
                                                              <i class="menu-bullet menu-bullet-dot">
                                                                  <span></span>
                                                              </i>
                                                              <span class="menu-text"><?= $child['sub_name'] ?></span>
                                                          </a>
                                                      </li>

                                                  <?php
                                                    }
                                                    ?>
                                              </ul>
                                          </div>
                                      </li>
                                      <?php
                                        ?>

                          <?php }
                                }
                            } ?>

                          </li>
                      </ul>
                  </div>
              </div>