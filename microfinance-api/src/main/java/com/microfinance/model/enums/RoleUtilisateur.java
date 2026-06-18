package com.microfinance.model.enums;

public enum RoleUtilisateur {
   ROLE_CLIENT,
   ROLE_GESTIONNAIRE,
   ROLE_SUPERVISOR,
   ROLE_ADMIN_SYSTEME,
   ROLE_ADMIN_BD,
   ROLE_SUPER_ADMIN;

   // $FF: synthetic method
   private static RoleUtilisateur[] $values() {
      return new RoleUtilisateur[]{ROLE_CLIENT, ROLE_GESTIONNAIRE, ROLE_SUPERVISOR, ROLE_ADMIN_SYSTEME, ROLE_ADMIN_BD, ROLE_SUPER_ADMIN};
   }
}
