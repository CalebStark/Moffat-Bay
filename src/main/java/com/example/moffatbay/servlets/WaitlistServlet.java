package com.example.moffatbay.servlets;

import java.io.IOException;
import java.io.PrintWriter;
import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.sql.SQLException;
import javax.servlet.ServletException;
import javax.servlet.annotation.WebServlet;
import javax.servlet.http.HttpServlet;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;
import com.google.gson.JsonObject;

public class WaitlistServlet extends HttpServlet {

    private static final String DB_URL = "jdbc:mysql://localhost:3306/moffat_bay_marina?useSSL=false&serverTimezone=UTC";
    private static final String DB_USER = "root";
    private static final String DB_PASSWORD = "";

    @Override
    protected void doGet(HttpServletRequest request, HttpServletResponse response) throws ServletException, IOException {

        response.setContentType("application/json");
        PrintWriter out = response.getWriter();

        try {
            Class.forName("com.mysql.cj.jdbc.Driver");
            Connection con = DriverManager.getConnection(DB_URL, DB_USER, DB_PASSWORD);

            String sql26 = "SELECT COUNT(*) FROM WaitList WHERE slipSize = 26";
            String sql40 = "SELECT COUNT(*) FROM WaitList WHERE slipSize = 40";
            String sql50 = "SELECT COUNT(*) FROM WaitList WHERE slipSize = 50";

            PreparedStatement pst26 = con.prepareStatement(sql26);
            PreparedStatement pst40 = con.prepareStatement(sql40);
            PreparedStatement pst50 = con.prepareStatement(sql50);

            ResultSet rs26 = pst26.executeQuery();
            ResultSet rs40 = pst40.executeQuery();
            ResultSet rs50 = pst50.executeQuery();

            int count26 = 0, count40 = 0, count50 = 0;
            if (rs26.next()) count26 = rs26.getInt(1);
            if (rs40.next()) count40 = rs40.getInt(1);
            if (rs50.next()) count50 = rs50.getInt(1);

            JsonObject jsonOutput = new JsonObject();
            jsonOutput.addProperty("count26", count26);
            jsonOutput.addProperty("count40", count40);
            jsonOutput.addProperty("count50", count50);

            out.print(jsonOutput.toString());
            out.flush();

            con.close();

        } catch (ClassNotFoundException | SQLException e) {
            System.err.println(e.getMessage());
            response.setStatus(500); // Internal Server Error
            JsonObject jsonOutput = new JsonObject();
            jsonOutput.addProperty("message", "Error getting waitlist counts.");
            out.print(jsonOutput.toString());
            out.flush();
        }
    }
}
